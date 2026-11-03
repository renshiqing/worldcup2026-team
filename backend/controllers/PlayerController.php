<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/PlayerController.php
 * 功能：后台球员管理（扩展模块）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\OperationLogger;
use common\models\FootballTeam;
use common\models\OperationLog;
use common\models\TeamPlayer;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 后台球员管理（扩展模块）。
 *
 * 对应数据表 team_player，A 的前台球员页也在使用同一批数据。
 */
class PlayerController extends AdminController
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
     * 球员列表，支持按球队筛选和姓名搜索。
     *
     * @return string
     */
    public function actionIndex()
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        $teamId = Yii::$app->request->get('team_id', '');

        $query = TeamPlayer::find()->with('team');

        if ($q !== '') {
            $query->andWhere(['like', 'name', $q]);
        }

        if ($teamId !== '' && $teamId !== null) {
            $query->andWhere(['team_id' => (int) $teamId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['team_id' => SORT_ASC, 'shirt_number' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'q' => $q,
            'teamId' => $teamId,
            'teamOptions' => ArrayHelper::map(
                FootballTeam::find()->orderBy(['name' => SORT_ASC])->all(),
                'id',
                'name'
            ),
        ]);
    }

    /**
     * 查看球员。
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
     * 新增球员。
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TeamPlayer();
        $model->is_captain = 0;
        $model->created_at = time();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $this->normalizeOptionalFields($model);

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_CREATE,
                    'player',
                    $model->id,
                    "新增球员：{$model->name}（{$model->team->name}）"
                );
                Yii::$app->session->setFlash('success', "球员“{$model->name}”已新增。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'teamOptions' => $this->teamOptions(),
        ]);
    }

    /**
     * 编辑球员。
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
            $this->normalizeOptionalFields($model);

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_UPDATE,
                    'player',
                    $model->id,
                    "修改球员：{$model->name}（{$model->team->name}）"
                );
                Yii::$app->session->setFlash('success', "球员“{$model->name}”已更新。");

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'teamOptions' => $this->teamOptions(),
        ]);
    }

    /**
     * 删除球员。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $playerId = $model->id;
        $model->delete();

        OperationLogger::log(
            OperationLog::ACTION_DELETE,
            'player',
            $playerId,
            "删除球员：{$name}"
        );

        Yii::$app->session->setFlash('success', "球员“{$name}”已删除。");

        return $this->redirect(['index']);
    }

    /**
     * 空字符串转为 null，避免唯一索引把 '' 当成号码。
     *
     * @param TeamPlayer $model
     */
    private function normalizeOptionalFields($model)
    {
        foreach (['shirt_number', 'position', 'nationality', 'birth_date'] as $attribute) {
            if ($model->$attribute === '') {
                $model->$attribute = null;
            }
        }
    }

    /**
     * @return array
     */
    private function teamOptions()
    {
        return ArrayHelper::map(
            FootballTeam::find()->orderBy(['name' => SORT_ASC])->all(),
            'id',
            'name'
        );
    }

    /**
     * @param int $id
     * @return TeamPlayer
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = TeamPlayer::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这位球员。');
        }

        return $model;
    }
}
