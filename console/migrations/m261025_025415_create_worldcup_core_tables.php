
<?php

use yii\db\Migration;

/**
 * Creates the core World Cup tables.
 */
class m261025_025415_create_worldcup_core_tables extends Migration
{
    /**
     * Apply migration without wrapping DDL in a transaction.
     */
    public function up()
    {
        // 1. 球队表
        $this->createTable('{{%football_team}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'country' => $this->string(80)->notNull(),
            'group_id' => $this->integer()->null(),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'uq-football_team-name',
            '{{%football_team}}',
            'name',
            true
        );

        $this->createIndex(
            'idx-football_team-group_id',
            '{{%football_team}}',
            'group_id'
        );

        $this->addForeignKey(
            'fk-football_team-group_id',
            '{{%football_team}}',
            'group_id',
            '{{%tournament_group}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // 2. 场馆表
        $this->createTable('{{%stadium}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'city_id' => $this->integer()->notNull(),
            'capacity' => $this->integer()->null(),
            'address' => $this->string(255)->null(),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'uq-stadium-name-city',
            '{{%stadium}}',
            ['name', 'city_id'],
            true
        );

        $this->addForeignKey(
            'fk-stadium-city_id',
            '{{%stadium}}',
            'city_id',
            '{{%host_city}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // 3. 比赛表
        $this->createTable('{{%football_match}}', [
            'id' => $this->primaryKey(),
            'stage_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->null(),
            'home_team_id' => $this->integer()->notNull(),
            'away_team_id' => $this->integer()->notNull(),
            'stadium_id' => $this->integer()->null(),
            'kickoff_at' => $this->dateTime()->notNull(),
            'home_score' => $this->smallInteger()->null(),
            'away_score' => $this->smallInteger()->null(),
            'status' => $this->string(20)
                ->notNull()
                ->defaultValue('scheduled'),
            'created_at' => $this->integer()
                ->notNull()
                ->defaultValue(0),
        ]);

        $this->createIndex(
            'idx-football_match-kickoff_at',
            '{{%football_match}}',
            'kickoff_at'
        );

        $this->createIndex(
            'idx-football_match-status',
            '{{%football_match}}',
            'status'
        );

        $this->createIndex(
            'idx-football_match-home_team_id',
            '{{%football_match}}',
            'home_team_id'
        );

        $this->createIndex(
            'idx-football_match-away_team_id',
            '{{%football_match}}',
            'away_team_id'
        );

        $this->addForeignKey(
            'fk-football_match-stage_id',
            '{{%football_match}}',
            'stage_id',
            '{{%match_stage}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-football_match-group_id',
            '{{%football_match}}',
            'group_id',
            '{{%tournament_group}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-football_match-home_team_id',
            '{{%football_match}}',
            'home_team_id',
            '{{%football_team}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-football_match-away_team_id',
            '{{%football_match}}',
            'away_team_id',
            '{{%football_team}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-football_match-stadium_id',
            '{{%football_match}}',
            'stadium_id',
            '{{%stadium}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        return true;
    }

    /**
     * Revert migration in reverse dependency order.
     */
    public function down()
    {
        $this->dropTable('{{%football_match}}');
        $this->dropTable('{{%stadium}}');
        $this->dropTable('{{%football_team}}');

        return true;
    }
}
