<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/TeamController.php
 * 功能：后台球队管理（列表、新增、查看、编辑、删除）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\CsvExporter;
use backend\components\OperationLogger;
use common\models\FootballTeam;
use common\models\OperationLog;
use common\models\TournamentGroup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 后台球队管理：列表、查看、新增、编辑、删除。
 *
 * 对应数据表 football_team，字段以 A 的迁移为准：
 * name、country、group_id、created_at。
 */
class TeamController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * 球队列表，支持按名称/国家和小组筛选。
     *
     * @return string
     */
    public function actionIndex()
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        $groupId = Yii::$app->request->get('group_id', '');

        $query = FootballTeam::find()->with('group');

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'name', $q],
                ['like', 'country', $q],
            ]);
        }

        if ($groupId !== '' && $groupId !== null) {
            $query->andWhere(['group_id' => (int) $groupId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'q' => $q,
            'groupId' => $groupId,
            'groupOptions' => TournamentGroup::options(),
        ]);
    }

    /**
     * 查看一支球队。
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增球队。
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new FootballTeam();
        $model->created_at = time();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_CREATE,
                    'team',
                    $model->id,
                    "新增球队：{$model->name}（{$model->groupName}）"
                );
                Yii::$app->session->setFlash('success', "球队“{$model->name}”已新增。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'groupOptions' => TournamentGroup::options(),
        ]);
    }

    /**
     * 编辑球队。
     *
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $createdAt = $model->created_at;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = $createdAt;

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_UPDATE,
                    'team',
                    $model->id,
                    "修改球队：{$model->name}（{$model->groupName}）"
                );
                Yii::$app->session->setFlash('success', "球队“{$model->name}”已更新。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'groupOptions' => TournamentGroup::options(),
        ]);
    }

    /**
     * 删除球队。球队被比赛引用时，数据库外键会阻止删除。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $teamId = $model->id;

        try {
            $model->delete();
            OperationLogger::log(
                OperationLog::ACTION_DELETE,
                'team',
                $teamId,
                "删除球队：{$name}"
            );
            Yii::$app->session->setFlash('success', "球队“{$name}”已删除。");
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash(
                'error',
                "球队“{$name}”已被比赛数据引用，不能直接删除。请先处理相关比赛。"
            );
        }

        return $this->redirect(['index']);
    }

    /**
     * 导出球队 CSV（遵循当前筛选条件）。
     *
     * @return \yii\web\Response
     */
    public function actionExport()
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        $groupId = Yii::$app->request->get('group_id', '');

        $query = FootballTeam::find()->with('group')->orderBy(['name' => SORT_ASC]);

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'name', $q],
                ['like', 'country', $q],
            ]);
        }

        if ($groupId !== '' && $groupId !== null) {
            $query->andWhere(['group_id' => (int) $groupId]);
        }

        $rows = [['ID', '球队名称', '国家/地区', '小组', '创建时间']];

        foreach ($query->all() as $team) {
            $rows[] = [
                $team->id,
                $team->name,
                $team->country,
                $team->groupName,
                date('Y-m-d H:i:s', (int) $team->created_at),
            ];
        }

        OperationLogger::log(
            OperationLog::ACTION_EXPORT,
            'team',
            null,
            '导出球队 CSV，共 ' . (count($rows) - 1) . ' 条'
        );

        return CsvExporter::send($rows, 'teams-' . date('Ymd-His') . '.csv');
    }

    /**
     * 找不到记录时抛出 404，而不是 PHP 报错。
     *
     * @param int $id
     * @return FootballTeam
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = FootballTeam::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这支球队。');
        }

        return $model;
    }
}
