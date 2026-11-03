<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/OperationLog.php
 * 功能：后台操作日志模型（operation_log）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 后台操作日志。
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $username
 * @property string $action
 * @property string $target_type
 * @property int|null $target_id
 * @property string $summary
 * @property string|null $ip
 * @property int $created_at
 */
class OperationLog extends ActiveRecord
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_STATUS = 'status';
    const ACTION_PUBLISH = 'publish';
    const ACTION_DRAFT = 'draft';
    const ACTION_EXPORT = 'export';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%operation_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['user_id', 'target_id', 'created_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['action'], 'string', 'max' => 20],
            [['target_type'], 'string', 'max' => 30],
            [['summary'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '日志编号',
            'user_id' => '用户编号',
            'username' => '操作人',
            'action' => '操作类型',
            'target_type' => '对象类型',
            'target_id' => '对象编号',
            'summary' => '操作说明',
            'ip' => 'IP 地址',
            'created_at' => '操作时间',
        ];
    }

    /**
     * 操作类型选项。
     *
     * @return array
     */
    public static function actionLabels()
    {
        return [
            self::ACTION_CREATE => '新增',
            self::ACTION_UPDATE => '修改',
            self::ACTION_DELETE => '删除',
            self::ACTION_STATUS => '审核',
            self::ACTION_PUBLISH => '发布',
            self::ACTION_DRAFT => '撤回',
            self::ACTION_EXPORT => '导出',
        ];
    }

    /**
     * 对象类型选项。
     *
     * @return array
     */
    public static function targetLabels()
    {
        return [
            'team' => '球队',
            'player' => '球员',
            'news' => '新闻',
            'message' => '留言',
            'team_profile' => '团队资料',
            'team_member' => '团队成员',
        ];
    }

    /**
     * 操作类型中文名称。
     *
     * @return string
     */
    public function getActionLabel()
    {
        $labels = self::actionLabels();

        return isset($labels[$this->action]) ? $labels[$this->action] : $this->action;
    }

    /**
     * 对象类型中文名称。
     *
     * @return string
     */
    public function getTargetLabel()
    {
        $labels = self::targetLabels();

        return isset($labels[$this->target_type]) ? $labels[$this->target_type] : $this->target_type;
    }

    /**
     * 操作类型的标签颜色。
     *
     * @return string
     */
    public function getActionBadge()
    {
        switch ($this->action) {
            case self::ACTION_CREATE:
                return 'label-success';
            case self::ACTION_UPDATE:
                return 'label-primary';
            case self::ACTION_DELETE:
                return 'label-danger';
            case self::ACTION_STATUS:
            case self::ACTION_PUBLISH:
            case self::ACTION_DRAFT:
                return 'label-warning';
            default:
                return 'label-default';
        }
    }
}
