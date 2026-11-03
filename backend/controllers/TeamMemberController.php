<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/TeamMemberController.php
 * 功能：后台团队成员管理
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\OperationLogger;
use common\models\OperationLog;
use common\models\TeamMember;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 后台团队成员管理（课程要求的“个人资料编辑”）。
 *
 * 对应数据表 team_member，前台团队主页由 B 读取展示。
 */
class TeamMemberController extends AdminController
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
     * 成员列表。
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TeamMember::find(),
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['display_order' => SORT_ASC, 'id' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 查看成员。
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
     * 新增成员。
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TeamMember();
        $model->display_order = (int) TeamMember::find()->max('display_order') + 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            OperationLogger::log(
                OperationLog::ACTION_CREATE,
                'team_member',
                $model->id,
                "新增团队成员：{$model->name}（{$model->role}）"
            );
            Yii::$app->session->setFlash('success', "成员“{$model->name}”已新增。");

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑成员。
     *
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            OperationLogger::log(
                OperationLog::ACTION_UPDATE,
                'team_member',
                $model->id,
                "修改团队成员：{$model->name}（{$model->role}）"
            );
            Yii::$app->session->setFlash('success', "成员“{$model->name}”已更新。");

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除成员。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $memberId = $model->id;
        $model->delete();

        OperationLogger::log(
            OperationLog::ACTION_DELETE,
            'team_member',
            $memberId,
            "删除团队成员：{$name}"
        );

        Yii::$app->session->setFlash('success', "成员“{$name}”已删除。");

        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return TeamMember
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = TeamMember::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这位成员。');
        }

        return $model;
    }
}
