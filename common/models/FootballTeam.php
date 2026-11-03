<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/FootballTeam.php
 * 功能：球队模型（football_team）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 参赛球队。
 *
 * 对应数据表 football_team，字段：name、country、group_id、created_at。
 * 这里没有 country_code、logo、description，后台表单不要再引用这些不存在的字段。
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property int|null $group_id
 * @property int $created_at
 *
 * @property TournamentGroup $group
 */
class FootballTeam extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%football_team}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'country'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['country'], 'string', 'max' => 80],
            [['name'], 'unique', 'message' => '已经存在同名球队。'],
            [['group_id', 'created_at'], 'integer'],
            [
                ['group_id'],
                'exist',
                'targetClass' => TournamentGroup::class,
                'targetAttribute' => ['group_id' => 'id'],
                'skipOnEmpty' => true,
                'message' => '选择的小组不存在。',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '球队编号',
            'name' => '球队名称',
            'country' => '所属国家/地区',
            'group_id' => '所在小组',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 所属小组。
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(TournamentGroup::class, ['id' => 'group_id']);
    }

    /**
     * 小组名称，没有分组时返回“未分组”，方便列表直接展示。
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->group === null ? '未分组' : $this->group->group_name . '组';
    }
}
