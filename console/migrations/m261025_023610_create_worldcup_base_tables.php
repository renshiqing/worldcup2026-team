<?php

use yii\db\Migration;

/**
 * Creates the initial World Cup business tables.
 */
class m261025_023610_create_worldcup_base_tables extends Migration
{
    /**
     * Apply migration.
     */
    public function up()
    {
        // 1. 主办城市表
        $this->createTable('{{%host_city}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'country' => $this->string(60)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'uq-host_city-name-country',
            '{{%host_city}}',
            ['name', 'country'],
            true
        );

        // 2. 世界杯小组表
        $this->createTable('{{%tournament_group}}', [
            'id' => $this->primaryKey(),
            'group_name' => $this->string(20)->notNull(),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'uq-tournament_group-group_name',
            '{{%tournament_group}}',
            'group_name',
            true
        );

        // 3. 比赛阶段表
        $this->createTable('{{%match_stage}}', [
            'id' => $this->primaryKey(),
            'stage_name' => $this->string(50)->notNull(),
            'stage_order' => $this->smallInteger()
                ->notNull()
                ->defaultValue(0),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'uq-match_stage-stage_name',
            '{{%match_stage}}',
            'stage_name',
            true
        );
    }

    /**
     * Revert migration.
     */
    public function down()
    {
        $this->dropTable('{{%match_stage}}');
        $this->dropTable('{{%tournament_group}}');
        $this->dropTable('{{%host_city}}');
    }
}