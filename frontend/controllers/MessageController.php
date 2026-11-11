<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/controllers/MessageController.php
 * 功能：前台球迷留言板：提交、成功提示、留言墙、我的留言（状态跟踪）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

namespace frontend\controllers;

use common\models\FanMessage;
use frontend\models\FanMessageForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * 球迷留言控制器。
 *
 * 前台提交的留言一律写入 fan_message，状态为 pending；
 * 后台审核通过后才会出现在“留言墙”（status = approved）。
 * 本控制器不提供任何修改状态的入口，审核权限只属于后台。
 */
class MessageController extends Controller
{
    /**
     * 会话中保存“我提交过的留言编号”的键名。
     */
    const SESSION_IDS = 'frontend.messageIds';

    /**
     * 会话中保存“最近一次提交的留言编号”的键名。
     */
    const SESSION_LAST_ID = 'frontend.lastMessageId';

    /**
     * 会话中保存“最近一次提交时间”的键名。
     */
    const SESSION_LAST_AT = 'frontend.lastMessageAt';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    // 留言只接受表单 POST，避免通过地址栏 GET 写入数据。
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 留言板首页：左侧留言表单，右侧已通过审核的留言。
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new FanMessageForm();

        return $this->render('index', [
            'model' => $model,
            'messages' => $this->findApprovedMessages(),
        ]);
    }

    /**
     * 提交留言。
     *
     * 校验失败时回到留言板并显示表单错误；
     * 保存成功后跳转到成功页，避免刷新重复提交。
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new FanMessageForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $lastSubmitAt = (int) Yii::$app->session->get(self::SESSION_LAST_AT, 0);

            if ($lastSubmitAt > 0 && time() - $lastSubmitAt < FanMessageForm::SUBMIT_INTERVAL) {
                $model->addError(
                    'message',
                    '留言提交太频繁，请等待 ' . FanMessageForm::SUBMIT_INTERVAL . ' 秒后再试。'
                );
            } elseif ($model->saveMessage()) {
                $id = (int) $model->id;

                $this->rememberMessage($id);
                Yii::$app->session->set(self::SESSION_LAST_ID, $id);
                Yii::$app->session->set(self::SESSION_LAST_AT, time());

                return $this->redirect(['success']);
            } else {
                $model->addError('message', '留言保存失败，请稍后重试。');
            }
        }

        return $this->render('index', [
            'model' => $model,
            'messages' => $this->findApprovedMessages(),
        ]);
    }

    /**
     * 留言提交成功页。
     *
     * 只展示当前会话刚刚提交的那条留言；
     * 直接访问本页（没有提交记录）时回到留言板。
     *
     * @return string|\yii\web\Response
     */
    public function actionSuccess()
    {
        $id = (int) Yii::$app->session->get(self::SESSION_LAST_ID, 0);

        if ($id <= 0) {
            return $this->redirect(['index']);
        }

        $message = FanMessage::findOne(['id' => $id]);

        if ($message === null) {
            return $this->redirect(['index']);
        }

        return $this->render('success', ['message' => $message]);
    }

    /**
     * 我的留言：按会话记录展示当前浏览器提交过的留言及审核状态。
     *
     * 后台把状态改为“已通过”并填写回复后，刷新本页即可看到变化，
     * 用来验证“前台提交 → 后台审核 → 前台显示”的闭环。
     *
     * @return string
     */
    public function actionMine()
    {
        $ids = Yii::$app->session->get(self::SESSION_IDS, []);
        $ids = is_array($ids) ? array_values(array_filter(array_map('intval', $ids))) : [];

        $messages = empty($ids)
            ? []
            : FanMessage::find()
                ->where(['id' => $ids])
                ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                ->all();

        return $this->render('mine', ['messages' => $messages]);
    }

    /**
     * 只展示已通过审核的留言，最新在前。
     *
     * @return FanMessage[]
     */
    private function findApprovedMessages()
    {
        return FanMessage::find()
            ->where(['status' => FanMessage::STATUS_APPROVED])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(FanMessageForm::WALL_LIMIT)
            ->all();
    }

    /**
     * 把留言编号记到会话里，最多保留最近 20 条。
     *
     * @param int $id
     * @return void
     */
    private function rememberMessage($id)
    {
        $ids = Yii::$app->session->get(self::SESSION_IDS, []);
        $ids = is_array($ids) ? array_map('intval', $ids) : [];

        array_unshift($ids, (int) $id);
        $ids = array_values(array_unique($ids));

        Yii::$app->session->set(self::SESSION_IDS, array_slice($ids, 0, 20));
    }
}
