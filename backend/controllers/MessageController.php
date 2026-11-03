<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/controllers/MessageController.php
 * 功能：后台留言管理（审核、回复、删除）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\controllers;

use backend\components\AdminController;
use backend\components\CsvExporter;
use backend\components\OperationLogger;
use common\models\FanMessage;
use common\models\OperationLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 后台留言处理：查看、审核（通过/拒绝）、回复、删除。
 *
 * 对应数据表 fan_message；admin_reply 与 replied_at 由 C 的迁移新增。
 */
class MessageController extends AdminController
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
                'status' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * 留言列表，默认显示全部，可按状态筛选。
     *
     * @return string
     */
    public function actionIndex()
    {
        $status = (string) Yii::$app->request->get('status', '');
        $q = trim((string) Yii::$app->request->get('q', ''));

        $query = FanMessage::find();

        if (array_key_exists($status, FanMessage::statuses())) {
            $query->andWhere(['status' => $status]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'nickname', $q],
                ['like', 'subject', $q],
                ['like', 'message', $q],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'q' => $q,
            'statusOptions' => FanMessage::statuses(),
        ]);
    }

    /**
     * 查看留言并填写回复。
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'statusOptions' => FanMessage::statuses(),
        ]);
    }

    /**
     * 保存回复与状态。
     *
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $hasReply = trim((string) $model->admin_reply) !== '';

            // 填了回复但状态仍是待审核时，自动标记为已通过。
            if ($hasReply && $model->status === FanMessage::STATUS_PENDING) {
                $model->status = FanMessage::STATUS_APPROVED;
            }

            if ($hasReply && empty($model->replied_at)) {
                $model->replied_at = time();
            }

            $model->updated_at = time();

            if ($model->save()) {
                OperationLogger::log(
                    OperationLog::ACTION_STATUS,
                    'message',
                    $model->id,
                    "处理留言：{$model->nickname}（{$model->statusLabel}）"
                );
                Yii::$app->session->setFlash('success', '留言处理结果已保存。');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'statusOptions' => FanMessage::statuses(),
        ]);
    }

    /**
     * 快速通过或拒绝。
     *
     * @param int $id
     * @param string $status approved|rejected
     * @return \yii\web\Response
     */
    public function actionStatus($id, $status)
    {
        $model = $this->findModel($id);

        if (!in_array($status, [FanMessage::STATUS_APPROVED, FanMessage::STATUS_REJECTED], true)) {
            Yii::$app->session->setFlash('error', '无效的留言状态。');

            return $this->redirect(['index']);
        }

        $model->status = $status;
        $model->updated_at = time();
        $model->save(false);

        OperationLogger::log(
            OperationLog::ACTION_STATUS,
            'message',
            $model->id,
            ($status === FanMessage::STATUS_APPROVED ? '通过留言：' : '拒绝留言：') . $model->nickname
        );

        Yii::$app->session->setFlash(
            'success',
            $status === FanMessage::STATUS_APPROVED ? '留言已通过审核。' : '留言已拒绝。'
        );

        return $this->redirect(['index']);
    }

    /**
     * 删除留言。
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $nickname = $model->nickname;
        $model->delete();

        OperationLogger::log(
            OperationLog::ACTION_DELETE,
            'message',
            $id,
            "删除留言：{$nickname}"
        );

        Yii::$app->session->setFlash('success', '留言已删除。');

        return $this->redirect(['index']);
    }

    /**
     * 导出留言 CSV（遵循当前筛选条件）。
     *
     * @return \yii\web\Response
     */
    public function actionExport()
    {
        $status = (string) Yii::$app->request->get('status', '');
        $q = trim((string) Yii::$app->request->get('q', ''));

        $query = FanMessage::find()->orderBy(['created_at' => SORT_DESC]);

        if (array_key_exists($status, FanMessage::statuses())) {
            $query->andWhere(['status' => $status]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'nickname', $q],
                ['like', 'subject', $q],
                ['like', 'message', $q],
            ]);
        }

        $rows = [[
            'ID',
            '昵称',
            '邮箱',
            '主题',
            '留言内容',
            '状态',
            '提交时间',
            '管理员回复',
            '回复时间',
        ]];

        foreach ($query->all() as $message) {
            $rows[] = [
                $message->id,
                $message->nickname,
                $message->email,
                $message->subject,
                $message->message,
                $message->statusLabel,
                date('Y-m-d H:i:s', (int) $message->created_at),
                $message->admin_reply,
                empty($message->replied_at) ? '' : date('Y-m-d H:i:s', (int) $message->replied_at),
            ];
        }

        OperationLogger::log(
            OperationLog::ACTION_EXPORT,
            'message',
            null,
            '导出留言 CSV，共 ' . (count($rows) - 1) . ' 条'
        );

        return CsvExporter::send($rows, 'messages-' . date('Ymd-His') . '.csv');
    }

    /**
     * @param int $id
     * @return FanMessage
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = FanMessage::findOne((int) $id);

        if ($model === null) {
            throw new NotFoundHttpException('没有找到这条留言。');
        }

        return $model;
    }
}
