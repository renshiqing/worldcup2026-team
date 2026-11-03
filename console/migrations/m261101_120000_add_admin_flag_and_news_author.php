<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/migrations/m261101_120000_add_admin_flag_and_news_author.php
 * 功能：迁移：管理员标记与新闻发布人
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

use yii\db\Migration;

/**
 * C（后台管理）新增字段：
 * 1. user.is_admin：区分管理员和普通注册用户，防止普通用户进入后台。
 * 2. news_article.author_id：记录新闻发布人。
 *
 * 只新增字段，不修改 A 已建好的表结构；执行后请让 A 重新导出 data/install.sql。
 */
class m261101_120000_add_admin_flag_and_news_author extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(
            '{{%user}}',
            'is_admin',
            $this->smallInteger()->notNull()->defaultValue(0)
        );

        $this->createIndex(
            'idx-user-is_admin',
            '{{%user}}',
            'is_admin'
        );

        $this->addColumn(
            '{{%news_article}}',
            'author_id',
            $this->integer()->null()
        );

        $this->createIndex(
            'idx-news_article-author_id',
            '{{%news_article}}',
            'author_id'
        );

        $this->addForeignKey(
            'fk-news_article-author_id',
            '{{%news_article}}',
            'author_id',
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
        $this->dropForeignKey('fk-news_article-author_id', '{{%news_article}}');
        $this->dropIndex('idx-news_article-author_id', '{{%news_article}}');
        $this->dropColumn('{{%news_article}}', 'author_id');

        $this->dropIndex('idx-user-is_admin', '{{%user}}');
        $this->dropColumn('{{%user}}', 'is_admin');
    }
}
