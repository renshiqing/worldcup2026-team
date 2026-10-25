<?php

use yii\db\Migration;

class m261025_063751_add_worldcup_extension_tables extends Migration
{
    // MySQL 的建表语句可能隐式提交，因此关闭迁移事务
  
    
    public function up()
    {
        // 1. 球员表
        $this->createTable('{{%team_player}}', [
            'id' => $this->primaryKey(),
            'team_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'shirt_number' => $this->smallInteger()->unsigned(),
            'position' => $this->string(30),
            'nationality' => $this->string(80),
            'birth_date' => $this->date(),
            'is_captain' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->createIndex(
            'uq-team_player-team-shirt',
            '{{%team_player}}',
            ['team_id', 'shirt_number'],
            true
        );

        $this->addForeignKey(
            'fk-team_player-team',
            '{{%team_player}}',
            'team_id',
            '{{%football_team}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 2. 比赛事件表
        $this->createTable('{{%match_event}}', [
            'id' => $this->primaryKey(),
            'match_id' => $this->integer()->notNull(),
            'team_id' => $this->integer(),
            'event_type' => $this->string(30)->notNull(),
            'minute' => $this->smallInteger()->unsigned(),
            'player_name' => $this->string(100),
            'description' => $this->string(255),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->createIndex(
            'idx-match_event-match-minute',
            '{{%match_event}}',
            ['match_id', 'minute']
        );

        $this->addForeignKey(
            'fk-match_event-match',
            '{{%match_event}}',
            'match_id',
            '{{%football_match}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-match_event-team',
            '{{%match_event}}',
            'team_id',
            '{{%football_team}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // 3. 新闻表
        $this->createTable('{{%news_article}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(200)->notNull(),
            'summary' => $this->text(),
            'content' => $this->text()->notNull(),
            'category' => $this->string(50)
                ->notNull()->defaultValue('赛事新闻'),
            'status' => $this->string(20)
                ->notNull()->defaultValue('draft'),
            'published_at' => $this->dateTime(),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->createIndex(
            'idx-news_article-status-published',
            '{{%news_article}}',
            ['status', 'published_at']
        );

        // 4. 球迷留言表
        $this->createTable('{{%fan_message}}', [
            'id' => $this->primaryKey(),
            'nickname' => $this->string(80)->notNull(),
            'email' => $this->string(120),
            'subject' => $this->string(200),
            'message' => $this->text()->notNull(),
            'status' => $this->string(20)
                ->notNull()->defaultValue('pending'),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->createIndex(
            'idx-fan_message-status-created',
            '{{%fan_message}}',
            ['status', 'created_at']
        );
    }

    public function down()
    {
        $this->dropTable('{{%fan_message}}');
        $this->dropTable('{{%news_article}}');
        $this->dropTable('{{%match_event}}');
        $this->dropTable('{{%team_player}}');
    }
}
