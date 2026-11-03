<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/TeamMember.php
 * 功能：课程团队成员模型（team_member）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 课程项目团队成员。
 *
 * 对应数据表 team_member，后台“成员管理”维护，
 * 前台团队主页由 B 读取展示。
 *
 * @property int $id
 * @property string $name
 * @property string|null $student_id
 * @property string|null $role
 * @property string|null $bio
 * @property string|null $avatar
 * @property int $display_order
 * @property int $created_at
 * @property int $updated_at
 */
class TeamMember extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%team_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['bio'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['student_id'], 'string', 'max' => 30],
            [['role'], 'string', 'max' => 50],
            [['avatar'], 'string', 'max' => 255],
            [['display_order', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '成员编号',
            'name' => '姓名',
            'student_id' => '学号',
            'role' => '项目分工',
            'bio' => '个人简介',
            'avatar' => '头像地址',
            'display_order' => '展示顺序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $now = time();
        $this->updated_at = $now;

        if ($insert) {
            $this->created_at = $now;
        }

        return true;
    }

    /**
     * 按展示顺序返回成员。
     *
     * @return static[]
     */
    public static function ordered()
    {
        return static::find()
            ->orderBy(['display_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }
}
