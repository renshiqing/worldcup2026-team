<?php

use yii\db\Migration;

class m261025_112207_seed_worldcup_demo_content extends Migration
{
    public function safeUp()
    {
        $now = time();
        $publishedAt = date('Y-m-d H:i:s', $now);

        // 1. 添加三条演示新闻
        $this->batchInsert(
            '{{%news_article}}',
            [
                'title',
                'summary',
                'content',
                'category',
                'status',
                'published_at',
                'created_at',
                'updated_at',
            ],
            [
                [
                    '世界杯赛事中心上线（演示）',
                    '介绍课程项目的赛事查询和数据展示功能。',
                    '本网站是基于 Yii2 和 MySQL 开发的世界杯主题课程项目，提供比赛列表、小组积分榜、球队详情及场馆信息查询。所有球队、赛程和比分均为演示数据，不代表官方赛事信息。',
                    '赛事新闻',
                    'published',
                    $publishedAt,
                    $now,
                    $now,
                ],
                [
                    '小组赛赛程查询功能说明（演示）',
                    '介绍比赛时间、比分和场馆信息的查询方式。',
                    '用户可以通过比赛列表查看演示赛程，并根据小组和比赛状态筛选比赛。后续可进一步扩展日期筛选和比赛详情查询功能。',
                    '赛事新闻',
                    'published',
                    $publishedAt,
                    $now,
                    $now,
                ],
                [
                    '世界杯小组积分榜规则（演示）',
                    '介绍演示积分榜的积分和排名计算方式。',
                    '本项目按照已结束且双方比分完整的小组赛计算演示积分榜。胜一场得3分，平局得1分，负场得0分，同时统计进球、失球和净胜球。',
                    '赛事新闻',
                    'published',
                    $publishedAt,
                    $now,
                    $now,
                ],
            ]
        );

        // 2. 添加两条演示留言
        $this->batchInsert(
            '{{%fan_message}}',
            [
                'nickname',
                'email',
                'subject',
                'message',
                'status',
                'created_at',
                'updated_at',
            ],
            [
                [
                    '演示球迷甲',
                    null,
                    '建议增加球员详情',
                    '希望球队详情页能够展示球员号码、位置和出生日期，方便查看球队阵容。',
                    'pending',
                    $now,
                    $now,
                ],
                [
                    '演示球迷乙',
                    'demo@example.com',
                    '建议增加赛程筛选',
                    '希望比赛页面能够支持按比赛日期筛选，方便快速找到感兴趣的比赛。',
                    'pending',
                    $now,
                    $now,
                ],
            ]
        );
    }

    public function safeDown()
    {
        // 只删除本次 migration 添加的演示留言
        $this->delete('{{%fan_message}}', [
            'nickname' => '演示球迷甲',
            'subject' => '建议增加球员详情',
        ]);

        $this->delete('{{%fan_message}}', [
            'nickname' => '演示球迷乙',
            'subject' => '建议增加赛程筛选',
        ]);

        // 只删除本次添加的三条演示新闻
        $this->delete('{{%news_article}}', [
            'title' => [
                '世界杯赛事中心上线（演示）',
                '小组赛赛程查询功能说明（演示）',
                '世界杯小组积分榜规则（演示）',
            ],
        ]);
    }
}
