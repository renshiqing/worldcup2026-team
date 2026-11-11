<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：console/migrations/m261111_120000_seed_team_demo_data.php
 * 功能：迁移：补充团队资料与三名成员的演示数据（只写数据，不改表结构）
 * 作者：B（前台页面与用户交互）
 * 日期：2026-11-11
 */

use yii\db\Migration;

/**
 * 前台首页和团队介绍页需要展示“三名成员和分工”。
 *
 * C 的 m261101_130000 只建表并插入了一条通用团队资料，
 * team_member 里没有成员数据，全新安装后首页会显示空状态。
 * 本迁移补上课程团队的演示数据：
 *
 * 1. team_profile 仍是 C 的默认名称时，更新为课程团队名称/口号/简介；
 * 2. team_member 为空时，插入三名成员（学号留空，管理员可在后台补填）。
 *
 * 迁移是幂等的：后台已经维护过资料或成员时不会覆盖、不会重复插入。
 * 只写数据，不改 A、C 已建好的表结构。
 */
class m261111_120000_seed_team_demo_data extends Migration
{
    /**
     * C 的迁移和 data/install.sql 里的默认团队名称。
     */
    const DEFAULT_TEAM_NAME = '世界杯赛事信息与球迷交流平台';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $now = time();

        $profileCount = (int) $this->db
            ->createCommand('SELECT COUNT(*) FROM {{%team_profile}}')
            ->queryScalar();

        if ($profileCount === 0) {
            $this->insert('{{%team_profile}}', [
                'team_name' => '越位三剑客',
                'slogan' => '代码不越位，需求必进球！',
                'description' => '三个人、一台数据库、一颗想捧杯的心。'
                    . '我们用 Yii2 + MySQL 搭建 2026 世界杯赛事信息与球迷交流平台：'
                    . 'A 负责数据库与比赛模块，B 负责前台页面与球迷留言，C 负责后台管理与统计。',
                'contact_email' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            // 只在团队资料还是默认值时更新，管理员已经改过的资料不会被覆盖。
            $this->update(
                '{{%team_profile}}',
                [
                    'team_name' => '越位三剑客',
                    'slogan' => '代码不越位，需求必进球！',
                    'description' => '三个人、一台数据库、一颗想捧杯的心。'
                        . '我们用 Yii2 + MySQL 搭建 2026 世界杯赛事信息与球迷交流平台：'
                        . 'A 负责数据库与比赛模块，B 负责前台页面与球迷留言，C 负责后台管理与统计。',
                    'updated_at' => $now,
                ],
                ['in', 'team_name', ['', self::DEFAULT_TEAM_NAME]]
            );
        }

        $memberCount = (int) $this->db
            ->createCommand('SELECT COUNT(*) FROM {{%team_member}}')
            ->queryScalar();

        if ($memberCount === 0) {
            $this->batchInsert(
                '{{%team_member}}',
                ['name', 'student_id', 'role', 'bio', 'avatar', 'display_order', 'created_at', 'updated_at'],
                [
                    [
                        '任诗清',
                        null,
                        '组长 · 数据库与比赛',
                        '负责数据库设计、比赛与场馆模块，以及三人代码的联调合并。',
                        null,
                        1,
                        $now,
                        $now,
                    ],
                    [
                        '张雅程',
                        null,
                        '前台页面与用户交互',
                        '负责首页、新闻与球迷留言等前台页面，整理用户操作手册和页面截图。',
                        null,
                        2,
                        $now,
                        $now,
                    ],
                    [
                        '钟一成',
                        null,
                        '后台管理与统计',
                        '负责后台菜单、球队与新闻 CRUD、留言审核和统计页面。',
                        null,
                        3,
                        $now,
                        $now,
                    ],
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 只删除本迁移插入的三名成员；团队资料可能被管理员改过，保留不动。
        $this->delete('{{%team_member}}', [
            'name' => ['任诗清', '张雅程', '钟一成'],
        ]);
    }
}
