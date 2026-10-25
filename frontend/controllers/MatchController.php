<?php
namespace frontend\controllers;

use yii\db\Query;
use yii\web\Controller;

class MatchController extends Controller
{
  
    public function actionIndex()
{
    // 1. 获取所有小组
    $groups = (new Query())
        ->select(['group_name'])
        ->from('{{%tournament_group}}')
        ->orderBy(['group_name' => SORT_ASC])
        ->column();

    $groupOptions = ['all' => '全部小组'];

    foreach ($groups as $groupName) {
        $groupOptions[$groupName] = $groupName . '组';
    }

    // 2. 定义可筛选的比赛状态
    $statusOptions = [
        'all' => '全部状态',
        'finished' => '已结束',
        'scheduled' => '待开赛',
        'live' => '进行中',
        'postponed' => '已延期',
        'cancelled' => '已取消',
    ];

    // 3. 读取用户选择的筛选条件
    $selectedGroup = \Yii::$app->request->get('group', 'all');
    $selectedStatus = \Yii::$app->request->get('status', 'all');

    // 校验输入，避免无效筛选值
    if (
        !is_string($selectedGroup) ||
        (
            $selectedGroup !== 'all' &&
            !in_array($selectedGroup, $groups, true)
        )
    ) {
        $selectedGroup = 'all';
    }

    if (
        !is_string($selectedStatus) ||
        !isset($statusOptions[$selectedStatus])
    ) {
        $selectedStatus = 'all';
    }

    // 4. 查询比赛及关联数据
    $query = (new Query())
        ->select([
            'm.id',
            'm.home_team_id',
            'm.away_team_id',
            'm.kickoff_at',
            'm.home_score',
            'm.away_score',
            'm.status',
            'stage_name' => 'stage.stage_name',
            'group_name' => 'grp.group_name',
            'home_team' => 'home.name',
            'away_team' => 'away.name',
            'stadium_name' => 'stadium.name',
            'city_name' => 'city.name',
           
        ])
        ->from(['m' => '{{%football_match}}'])
        ->innerJoin(
            ['stage' => '{{%match_stage}}'],
            'stage.id = m.stage_id'
        )
        ->leftJoin(
            ['grp' => '{{%tournament_group}}'],
            'grp.id = m.group_id'
        )
        ->innerJoin(
            ['home' => '{{%football_team}}'],
            'home.id = m.home_team_id'
        )
        ->innerJoin(
            ['away' => '{{%football_team}}'],
            'away.id = m.away_team_id'
        )
        ->leftJoin(
            ['stadium' => '{{%stadium}}'],
            'stadium.id = m.stadium_id'
        )
        ->leftJoin(
            ['city' => '{{%host_city}}'],
            'city.id = stadium.city_id'
        );

    // 5. 按小组筛选
    if ($selectedGroup !== 'all') {
        $query->andWhere([
            'grp.group_name' => $selectedGroup,
        ]);
    }

    // 6. 按比赛状态筛选
    if ($selectedStatus !== 'all') {
        $query->andWhere([
            'm.status' => $selectedStatus,
        ]);
    }

    // 7. 按开赛时间排序
    $matches = $query
        ->orderBy([
            'm.kickoff_at' => SORT_ASC,
            'm.id' => SORT_ASC,
        ])
        ->all();

    // 8. 将数据和筛选条件传给页面
    return $this->render('index', [
        'matches' => $matches,
        'groupOptions' => $groupOptions,
        'selectedGroup' => $selectedGroup,
        'statusOptions' => $statusOptions,
        'selectedStatus' => $selectedStatus,
    ]);
}
    public function actionView($id)
{
    $match = (new \yii\db\Query())
        ->select([
            'm.id',
            'm.kickoff_at',
            'm.home_score',
            'm.away_score',
            'm.status',
            'm.home_team_id',
            'm.away_team_id',
            'home_team_name' => 'ht.name',
            'home_country' => 'ht.country',
            'away_team_name' => 'at.name',
            'away_country' => 'at.country',
            'stage_name' => 'ms.stage_name',
            'group_name' => 'tg.group_name',
            'stadium_name' => 's.name',
            'city_name' => 'c.name',
            'city_country' => 'c.country',
        ])
        ->from(['m' => 'football_match'])
        ->innerJoin(['ms' => 'match_stage'], 'ms.id = m.stage_id')
        ->leftJoin(['tg' => 'tournament_group'], 'tg.id = m.group_id')
        ->innerJoin(['ht' => 'football_team'], 'ht.id = m.home_team_id')
        ->innerJoin(['at' => 'football_team'], 'at.id = m.away_team_id')
        ->leftJoin(['s' => 'stadium'], 's.id = m.stadium_id')
        ->leftJoin(['c' => 'host_city'], 'c.id = s.city_id')
        ->where(['m.id' => (int)$id])
        ->one();

    if (!$match) {
        throw new \yii\web\NotFoundHttpException('找不到该比赛。');
    }

    return $this->render('view', [
        'match' => $match,
    ]);
}
    public function actionStandings()
    {
        $groups = (new Query())
            ->from('{{%tournament_group}}')
            ->orderBy(['group_name' => SORT_ASC])
            ->all();

        $standings = [];

        foreach ($groups as $group) {
            $teams = (new Query())
                ->select(['id', 'name', 'country'])
                ->from('{{%football_team}}')
                ->where(['group_id' => $group['id']])
                ->all();

            if (empty($teams)) {
                continue;
            }

            $stats = [];

            foreach ($teams as $team) {
                $id = (int) $team['id'];

                $stats[$id] = [
                    'name' => $team['name'],
                    'country' => $team['country'],
                    'played' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                    'points' => 0,
                ];
            }

            $matches = (new Query())
                ->select([
                    'home_team_id',
                    'away_team_id',
                    'home_score',
                    'away_score',
                ])
                ->from('{{%football_match}}')
                ->where([
                    'group_id' => $group['id'],
                    'status' => 'finished',
                ])
                ->all();

            foreach ($matches as $match) {
                if (
                    $match['home_score'] === null ||
                    $match['away_score'] === null
                ) {
                    continue;
                }

                $homeId = (int) $match['home_team_id'];
                $awayId = (int) $match['away_team_id'];

                if (
                    !isset($stats[$homeId]) ||
                    !isset($stats[$awayId])
                ) {
                    continue;
                }

                $hs = (int) $match['home_score'];
                $as = (int) $match['away_score'];

                $stats[$homeId]['played']++;
                $stats[$awayId]['played']++;

                $stats[$homeId]['goals_for'] += $hs;
                $stats[$homeId]['goals_against'] += $as;
                $stats[$awayId]['goals_for'] += $as;
                $stats[$awayId]['goals_against'] += $hs;

                if ($hs > $as) {
                    $stats[$homeId]['wins']++;
                    $stats[$awayId]['losses']++;
                    $stats[$homeId]['points'] += 3;
                } elseif ($hs < $as) {
                    $stats[$awayId]['wins']++;
                    $stats[$homeId]['losses']++;
                    $stats[$awayId]['points'] += 3;
                } else {
                    $stats[$homeId]['draws']++;
                    $stats[$awayId]['draws']++;
                    $stats[$homeId]['points']++;
                    $stats[$awayId]['points']++;
                }
            }

            foreach ($stats as &$row) {
                $row['goal_difference'] =
                    $row['goals_for'] - $row['goals_against'];
            }
            unset($row);

            $rows = array_values($stats);

            usort($rows, function ($a, $b) {
                return ($b['points'] <=> $a['points'])
                    ?: ($b['goal_difference'] <=> $a['goal_difference'])
                    ?: ($b['goals_for'] <=> $a['goals_for'])
                    ?: strcmp($a['name'], $b['name']);
            });

            $standings[] = [
                'group_name' => $group['group_name'],
                'teams' => $rows,
            ];
        }

        return $this->render('standings', [
            'standings' => $standings,
        ]);
    }
}
