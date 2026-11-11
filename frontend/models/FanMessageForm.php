<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：frontend/models/FanMessageForm.php
 * 功能：前台留言表单模型（复用 fan_message 表，补充表单校验与防刷）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

namespace frontend\models;

use common\models\FanMessage;

/**
 * 前台留言表单。
 *
 * 继承 C 的 FanMessage 模型，避免字段与后台不一致；
 * 只增加表单专用字段（website 蜜罐）和前台校验规则，
 * 不修改数据表结构，也不修改 C 的模型文件。
 *
 * @property string|null $website 蜜罐字段，正常用户看不到也不需要填写
 */
class FanMessageForm extends FanMessage
{
    /**
     * 留言板一次最多展示的留言条数。
     */
    const WALL_LIMIT = 10;

    /**
     * 留言内容允许的最大长度。
     */
    const MESSAGE_MAX_LENGTH = 2000;

    /**
     * 两次提交之间的最短间隔（秒），防止重复点击和简单刷屏。
     */
    const SUBMIT_INTERVAL = 10;

    /**
     * 蜜罐字段：机器人往往会填写所有输入框，正常用户看不到它。
     *
     * @var string|null
     */
    public $website;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            [
                // 先去掉首尾空格，避免“全是空格”的留言通过必填校验。
                [['nickname', 'email', 'subject', 'message'], 'trim'],
            ],
            parent::rules(),
            [
                [['nickname'], 'string', 'min' => 2, 'max' => 80,
                    'tooShort' => '昵称至少 2 个字符。'],
                [['message'], 'string', 'min' => 5, 'max' => self::MESSAGE_MAX_LENGTH,
                    'tooShort' => '留言内容至少 5 个字符。',
                    'tooLong' => '留言内容不能超过 ' . self::MESSAGE_MAX_LENGTH . ' 个字符。'],
                [['subject'], 'string', 'max' => 200],
                [
                    ['website'],
                    function ($attribute) {
                        if ($this->$attribute !== null && $this->$attribute !== '') {
                            $this->addError($attribute, '检测到异常提交，请刷新页面后重试。');
                        }
                    },
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'nickname' => '昵称',
            'email' => '邮箱（选填）',
            'subject' => '留言主题（选填）',
            'message' => '留言内容',
            'website' => '网址',
        ]);
    }

    /**
     * 保存留言：新留言一律进入“待审核”，由后台审核后才在前台展示。
     *
     * FanMessage 没有 beforeSave，所以这里手动写创建/更新时间，
     * 与 C 的后台逻辑保持一致（后台读取的是同两个字段）。
     *
     * @return bool
     */
    public function saveMessage()
    {
        $now = time();

        $this->status = self::STATUS_PENDING;
        $this->created_at = $now;
        $this->updated_at = $now;
        $this->admin_reply = null;
        $this->replied_at = null;

        // 控制器已经执行过 validate()，这里跳过重复校验，直接写入。
        return $this->save(false);
    }
}
