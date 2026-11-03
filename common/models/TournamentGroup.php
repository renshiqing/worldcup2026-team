<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/TournamentGroup.php
 * 功能：小组模型（tournament_group）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use yii\db\ActiveRecord;

/**
 * 世界杯小组（A-L）。
 *
 * 对应数据表 tournament_group。
 */
class TournamentGroup extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tournament_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_name'], 'required'],
            [['group_name'], 'string', 'max' => 20],
            [['group_name'], 'unique'],
            [['created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '小组编号',
            'group_name' => '小组名称',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 返回 [id => "A组"] 形式的下拉选项。
     *
     * @return array
     */
    public static function options()
    {
        $options = [];

        foreach (static::find()->orderBy(['group_name' => SORT_ASC])->all() as $group) {
            $options[$group->id] = $group->group_name . '组';
        }

        return $options;
    }
}
