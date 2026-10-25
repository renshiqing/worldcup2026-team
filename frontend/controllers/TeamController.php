<?php
namespace frontend\controllers;

use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeamController extends Controller
{
    // 球队列表
    public function actionIndex()
    {
        $teams = (new Query())
            ->select([
                't.id',
                't.name',
                't.country',
                'g.group_name',
            ])
            ->from(['t' => 'football_team'])
            ->leftJoin(
                ['g' => 'tournament_group'],
                'g.id = t.group_id'
            )
            ->orderBy([
                'g.group_name' => SORT_ASC,
                't.name' => SORT_ASC,
            ])
            ->all();

        return $this->render('index', [
            'teams' => $teams,
        ]);
    }

    // 球队详情及相关比赛
    public function actionView($id)
    {
        $id = (int)$id;

        // 查询球队基本信息
        $team = (new Query())
            ->select([
                't.id',
                't.name',
                't.country',
                'g.group_name',
            ])
            ->from(['t' => 'football_team'])
            ->leftJoin(
                ['g' => 'tournament_group'],
                'g.id = t.group_id'
            )
            ->where(['t.id' => $id])
            ->one();

        if ($team === false) {
            throw new NotFoundHttpException('没有找到该球队。');
        }

        // 查询该球队作为主队或客队参加的所有比赛
        $matches = (new Query())
            ->select([
                'fm.id',
                'fm.home_team_id',
                'fm.away_team_id',
                'fm.kickoff_at',
                'fm.home_score',
                'fm.away_score',
                'fm.status',
                'stage_name' => 'ms.stage_name',
                'group_name' => 'g.group_name',
                'home_team_name' => 'home.name',
                'away_team_name' => 'away.name',
                'stadium_name' => 's.name',
                'city_name' => 'c.name',
            ])
            ->from(['fm' => 'football_match'])
            ->leftJoin(
                ['ms' => 'match_stage'],
                'ms.id = fm.stage_id'
            )
            ->leftJoin(
                ['g' => 'tournament_group'],
                'g.id = fm.group_id'
            )
            ->leftJoin(
                ['home' => 'football_team'],
                'home.id = fm.home_team_id'
            )
            ->leftJoin(
                ['away' => 'football_team'],
                'away.id = fm.away_team_id'
            )
            ->leftJoin(
                ['s' => 'stadium'],
                's.id = fm.stadium_id'
            )
            ->leftJoin(
                ['c' => 'host_city'],
                'c.id = s.city_id'
            )
            ->where([
                'or',
                ['fm.home_team_id' => $id],
                ['fm.away_team_id' => $id],
            ])
            ->orderBy([
                'fm.kickoff_at' => SORT_ASC,
                'fm.id' => SORT_ASC,
            ])
            ->all();

        return $this->render('view', [
            'team' => $team,
            'matches' => $matches,
        ]);
    }
}
