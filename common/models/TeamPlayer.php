<?php
namespace common\models;

use yii\db\ActiveRecord;

class TeamPlayer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%team_player}}';
    }

    public function rules()
    {
        return [
            [['team_id', 'name'], 'required'],
            [['team_id', 'is_captain', 'created_at'], 'integer'],
            [['shirt_number'], 'integer', 'min' => 0, 'max' => 65535],
            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],
            [['is_captain'], 'in', 'range' => [0, 1]],
            [['name'], 'string', 'max' => 100],
            [['position'], 'string', 'max' => 30],
            [['nationality'], 'string', 'max' => 80],
            [
                ['team_id', 'shirt_number'],
                'unique',
                'targetAttribute' => ['team_id', 'shirt_number'],
                'message' => '同一支球队不能有重复的球衣号码。',
                'when' => static function ($model) {
                    return $model->shirt_number !== null && $model->shirt_number !== '';
                },
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '球员编号',
            'team_id' => '所属球队',
            'name' => '球员姓名',
            'shirt_number' => '球衣号码',
            'position' => '场上位置',
            'nationality' => '国籍/地区',
            'birth_date' => '出生日期',
            'is_captain' => '是否队长',
            'created_at' => '创建时间',
        ];
    }
}
