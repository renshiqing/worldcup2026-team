<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/migrations/m261101_140000_create_operation_log_table.php
 * 功能：迁移：后台操作日志表
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

use yii\db\Migration;

/**
 * 后台操作日志：记录谁在什么时候新增/修改/删除了什么。
 *
 * 只新增表，不改 A 已有结构。日志只追加，不提供修改和删除入口。
 */
class m261101_140000_create_operation_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%operation_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'username' => $this->string(50)->notNull()->defaultValue(''),
            'action' => $this->string(20)->notNull(),
            'target_type' => $this->string(30)->notNull()->defaultValue(''),
            'target_id' => $this->integer()->null(),
            'summary' => $this->string(255)->notNull()->defaultValue(''),
            'ip' => $this->string(45)->null(),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex(
            'idx-operation_log-created_at',
            '{{%operation_log}}',
            'created_at'
        );

        $this->createIndex(
            'idx-operation_log-action',
            '{{%operation_log}}',
            'action'
        );

        $this->createIndex(
            'idx-operation_log-target',
            '{{%operation_log}}',
            ['target_type', 'target_id']
        );

        $this->addForeignKey(
            'fk-operation_log-user_id',
            '{{%operation_log}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%operation_log}}');
    }
}
