<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/migrations/m261101_130000_create_team_profile_tables.php
 * 功能：迁移：团队资料与团队成员表
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

use yii\db\Migration;

/**
 * 课程要求补齐：团队主页需要“团队/个人资料编辑”。
 *
 * 新增两张表：
 * 1. team_profile：课程项目团队的基本资料（单行）；
 * 2. team_member：三名成员的姓名、学号、分工、简介。
 *
 * 只新增表，不改 A 已有表结构。
 */
class m261101_130000_create_team_profile_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->createTable('{{%team_profile}}', [
            'id' => $this->primaryKey(),
            'team_name' => $this->string(100)->notNull()->defaultValue(''),
            'slogan' => $this->string(200)->null(),
            'description' => $this->text()->null(),
            'contact_email' => $this->string(120)->null(),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('{{%team_member}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'student_id' => $this->string(30)->null(),
            'role' => $this->string(50)->null(),
            'bio' => $this->text()->null(),
            'avatar' => $this->string(255)->null(),
            'display_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx-team_member-display_order',
            '{{%team_member}}',
            'display_order'
        );

        // 先放一条通用团队资料，成员资料由管理员在后台填写真实信息。
        $this->insert('{{%team_profile}}', [
            'team_name' => '世界杯赛事信息与球迷交流平台',
            'slogan' => '数据驱动，服务球迷',
            'description' => '基于 Yii2 与 MariaDB 的课程团队项目，提供球队、比赛、新闻和球迷留言功能。',
            'contact_email' => '',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%team_member}}');
        $this->dropTable('{{%team_profile}}');
    }
}
