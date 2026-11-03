<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/FanMessage.php
 * 功能：留言模型（fan_message），审核状态
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 球迷留言。
 *
 * 对应数据表 fan_message。admin_reply、replied_at 由 C 的迁移
 * m261101_120100 新增，用于后台回复留言。
 *
 * @property int $id
 * @property string $nickname
 * @property string|null $email
 * @property string|null $subject
 * @property string $message
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $admin_reply
 * @property int|null $replied_at
 */
class FanMessage extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%fan_message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nickname', 'message'], 'required'],
            [['message', 'admin_reply'], 'string'],
            [['nickname'], 'string', 'max' => 80],
            [['subject'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 120],
            [['email'], 'email'],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => array_keys(self::statuses())],
            [['created_at', 'updated_at', 'replied_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '留言编号',
            'nickname' => '昵称',
            'email' => '邮箱',
            'subject' => '主题',
            'message' => '留言内容',
            'status' => '处理状态',
            'created_at' => '提交时间',
            'updated_at' => '更新时间',
            'admin_reply' => '管理员回复',
            'replied_at' => '回复时间',
        ];
    }

    /**
     * 状态选项。
     *
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
        ];
    }

    /**
     * 状态对应的 Bootstrap 标签颜色。
     *
     * @return string
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case self::STATUS_APPROVED:
                return 'label-success';
            case self::STATUS_REJECTED:
                return 'label-danger';
            default:
                return 'label-warning';
        }
    }

    /**
     * 状态中文名称。
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statuses = self::statuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : $this->status;
    }
}
