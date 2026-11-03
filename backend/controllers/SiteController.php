<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/SiteController.php
 * 功能：后台登录与统计概览首页
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\web\ForbiddenHttpException;
use common\models\LoginForm;
use common\models\FootballTeam;
use common\models\NewsArticle;
use common\models\FanMessage;
use common\models\TeamPlayer;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 后台首页：统计数字、小组球队分布、最近留言和新闻。
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->isAdmin()) {
            throw new ForbiddenHttpException('当前账号不是管理员，不能进入后台管理页面。');
        }

        $teamCount = FootballTeam::find()->count();
        $matchCount = (new Query())->from('{{%football_match}}')->count();
        $newsCount = NewsArticle::find()->count();
        $playerCount = TeamPlayer::find()->count();
        $newsPublishedCount = NewsArticle::find()
            ->where(['status' => NewsArticle::STATUS_PUBLISHED])
            ->count();
        $newsDraftCount = max(0, $newsCount - $newsPublishedCount);

        // 留言按状态统计：待审核 / 已通过 / 已拒绝。
        $messageCounts = [
            FanMessage::STATUS_PENDING => 0,
            FanMessage::STATUS_APPROVED => 0,
            FanMessage::STATUS_REJECTED => 0,
        ];

        $messageRows = (new Query())
            ->select(['status', 'cnt' => 'COUNT(*)'])
            ->from('{{%fan_message}}')
            ->groupBy('status')
            ->all();

        foreach ($messageRows as $row) {
            if (isset($messageCounts[$row['status']])) {
                $messageCounts[$row['status']] = (int) $row['cnt'];
            }
        }

        $messageTotal = array_sum($messageCounts);
        $pendingCount = $messageCounts[FanMessage::STATUS_PENDING];

        // 系统信息：数据库名、表数量、迁移数量，答辩时可以直接说明数据基础。
        $dbName = Yii::$app->db->createCommand('SELECT DATABASE()')->queryScalar();
        $tableCount = (new Query())
            ->from('information_schema.tables')
            ->where(['table_schema' => $dbName])
            ->count();
        $migrationCount = (new Query())
            ->from('{{%migration}}')
            ->count();

        // 各小组球队数量，用于后台首页的柱状展示。
        $groupRows = (new Query())
            ->select([
                'group_name' => 'g.group_name',
                'team_count' => 'COUNT(t.id)',
            ])
            ->from(['g' => '{{%tournament_group}}'])
            ->leftJoin(['t' => '{{%football_team}}'], 't.group_id = g.id')
            ->groupBy(['g.id', 'g.group_name'])
            ->orderBy(['g.group_name' => SORT_ASC])
            ->all();

        // 只展示有球队的小组，避免 12 个小组里一大半是 0，页面显得很空。
        $visibleGroups = [];
        $groupedTeamCount = 0;

        foreach ($groupRows as $row) {
            $count = (int) $row['team_count'];

            if ($count > 0) {
                $row['team_count'] = $count;
                $visibleGroups[] = $row;
                $groupedTeamCount += $count;
            }
        }

        $ungroupedCount = FootballTeam::find()
            ->where(['group_id' => null])
            ->count();

        $maxTeamCount = 1;

        foreach ($visibleGroups as $row) {
            $maxTeamCount = max($maxTeamCount, (int) $row['team_count']);
        }

        $recentMessages = FanMessage::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // 近 7 天留言趋势（按服务器本地日期分组）。
        $trendStart = strtotime(date('Y-m-d', strtotime('-6 days')));
        $trendCounts = [];

        $trendRows = FanMessage::find()
            ->select(['created_at'])
            ->where(['>=', 'created_at', $trendStart])
            ->asArray()
            ->all();

        foreach ($trendRows as $row) {
            $day = date('Y-m-d', (int) $row['created_at']);
            $trendCounts[$day] = isset($trendCounts[$day]) ? $trendCounts[$day] + 1 : 1;
        }

        $messageTrend = [];
        $trendTotal = 0;

        for ($i = 6; $i >= 0; $i--) {
            $timestamp = strtotime("-{$i} days");
            $day = date('Y-m-d', $timestamp);
            $count = isset($trendCounts[$day]) ? $trendCounts[$day] : 0;
            $trendTotal += $count;

            $messageTrend[] = [
                'date' => $day,
                'label' => date('m-d', $timestamp),
                'count' => $count,
            ];
        }

        $trendMax = 1;

        foreach ($messageTrend as $item) {
            $trendMax = max($trendMax, $item['count']);
        }

        $recentNews = NewsArticle::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'teamCount' => $teamCount,
            'matchCount' => $matchCount,
            'newsCount' => $newsCount,
            'playerCount' => $playerCount,
            'pendingCount' => $pendingCount,
            'newsPublishedCount' => $newsPublishedCount,
            'newsDraftCount' => $newsDraftCount,
            'messageCounts' => $messageCounts,
            'messageTotal' => $messageTotal,
            'messageTrend' => $messageTrend,
            'trendTotal' => $trendTotal,
            'trendMax' => $trendMax,
            'dbName' => $dbName,
            'tableCount' => $tableCount,
            'migrationCount' => $migrationCount,
            'groupRows' => $groupRows,
            'visibleGroups' => $visibleGroups,
            'groupedTeamCount' => $groupedTeamCount,
            'ungroupedCount' => $ungroupedCount,
            'maxTeamCount' => $maxTeamCount,
            'recentMessages' => $recentMessages,
            'recentNews' => $recentNews,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
