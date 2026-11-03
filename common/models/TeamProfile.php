<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/TeamProfile.php
 * 功能：课程团队资料模型（team_profile）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 课程项目团队资料（单行）。
 *
 * 对应数据表 team_profile，后台“团队资料”页面编辑，
 * 前台团队主页由 B 读取展示。
 *
 * @property int $id
 * @property string $team_name
 * @property string|null $slogan
 * @property string|null $description
 * @property string|null $contact_email
 * @property int $created_at
 * @property int $updated_at
 */
class TeamProfile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%team_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_name'], 'required'],
            [['description'], 'string'],
            [['team_name'], 'string', 'max' => 100],
            [['slogan'], 'string', 'max' => 200],
            [['contact_email'], 'string', 'max' => 120],
            [['contact_email'], 'email'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'team_name' => '团队名称',
            'slogan' => '团队口号',
            'description' => '团队简介',
            'contact_email' => '联系邮箱',
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
     * 取团队资料；没有记录时返回一个带默认值的新对象。
     *
     * @return static
     */
    public static function current()
    {
        $model = static::find()->orderBy(['id' => SORT_ASC])->one();

        if ($model === null) {
            $model = new static();
            $model->team_name = '世界杯赛事信息与球迷交流平台';
        }

        return $model;
    }
}
