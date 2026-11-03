<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/migrations/m261101_120100_add_reply_to_fan_message.php
 * 功能：迁移：留言管理员回复字段
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

use yii\db\Migration;

/**
 * C（后台管理）新增字段：留言的管理员回复与回复时间。
 *
 * 只给 fan_message 追加字段，不影响前台已有留言数据。
 * 执行后请让 A 重新导出 data/install.sql。
 */
class m261101_120100_add_reply_to_fan_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(
            '{{%fan_message}}',
            'admin_reply',
            $this->text()->null()
        );

        $this->addColumn(
            '{{%fan_message}}',
            'replied_at',
            $this->integer()->null()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%fan_message}}', 'replied_at');
        $this->dropColumn('{{%fan_message}}', 'admin_reply');
    }
}
