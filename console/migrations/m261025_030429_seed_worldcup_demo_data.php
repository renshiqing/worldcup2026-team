
<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Seed demo teams, stadiums and matches.
 * All teams and scores in this migration are fictional.
 */
class m261025_030429_seed_worldcup_demo_data extends Migration
{
    public function up()
    {
        $now = time();
        $teamIds = [];
        $stadiumIds = [];

        // 1. 插入演示球队
        foreach ($this->teamDefinitions() as $team) {
            $groupId = $this->findId(
                '{{%tournament_group}}',
                ['group_name' => $team['group_name']]
            );

            if ($groupId === null) {
                throw new \RuntimeException(
                    'Missing group: ' . $team['group_name']
                );
            }

            $this->insertIfMissing(
                '{{%football_team}}',
                ['name' => $team['name']],
                [
                    'name' => $team['name'],
                    'country' => $team['country'],
                    'group_id' => $groupId,
                    'created_at' => $now,
                ]
            );

            $teamIds[$team['name']] = $this->findId(
                '{{%football_team}}',
                ['name' => $team['name']]
            );
        }

        // 2. 插入演示场馆
        foreach ($this->stadiumDefinitions() as $stadium) {
            $cityId = $this->findId(
                '{{%host_city}}',
                [
                    'name' => $stadium['city_name'],
                    'country' => $stadium['country'],
                ]
            );

            if ($cityId === null) {
                throw new \RuntimeException(
                    'Missing host city: ' . $stadium['city_name']
                );
            }

            $condition = [
                'name' => $stadium['name'],
                'city_id' => $cityId,
            ];

            $this->insertIfMissing(
                '{{%stadium}}',
                $condition,
                [
                    'name' => $stadium['name'],
                    'city_id' => $cityId,
                    'capacity' => $stadium['capacity'],
                    'address' => $stadium['address'],
                    'created_at' => $now,
                ]
            );

            $stadiumIds[$stadium['name']] = $this->findId(
                '{{%stadium}}',
                $condition
            );
        }

        // 3. 获取比赛阶段
        $stageIds = [];

        foreach (['小组赛', '32强赛'] as $stageName) {
            $stageIds[$stageName] = $this->findId(
                '{{%match_stage}}',
                ['stage_name' => $stageName]
            );

            if ($stageIds[$stageName] === null) {
                throw new \RuntimeException(
                    'Missing match stage: ' . $stageName
                );
            }
        }

        // 4. 插入比赛数据
        foreach ($this->matchDefinitions() as $match) {
            $stageId = $stageIds[$match['stage']];
            $homeId = $teamIds[$match['home']];
            $awayId = $teamIds[$match['away']];
            $stadiumId = $stadiumIds[$match['stadium']];

            $groupId = null;

            if ($match['group_name'] !== null) {
                $groupId = $this->findId(
                    '{{%tournament_group}}',
                    ['group_name' => $match['group_name']]
                );
            }

            $condition = [
                'stage_id' => $stageId,
                'home_team_id' => $homeId,
                'away_team_id' => $awayId,
                'kickoff_at' => $match['kickoff_at'],
            ];

            $this->insertIfMissing(
                '{{%football_match}}',
                $condition,
                [
                    'stage_id' => $stageId,
                    'group_id' => $groupId,
                    'home_team_id' => $homeId,
                    'away_team_id' => $awayId,
                    'stadium_id' => $stadiumId,
                    'kickoff_at' => $match['kickoff_at'],
                    'home_score' => $match['home_score'],
                    'away_score' => $match['away_score'],
                    'status' => $match['status'],
                    'created_at' => $now,
                ]
            );
        }

        echo "Demo data seeded successfully.\n";
        return true;
    }

    public function down()
    {
        // 先删除本迁移添加的比赛
        foreach ($this->matchDefinitions() as $match) {
            $stageId = $this->findId(
                '{{%match_stage}}',
                ['stage_name' => $match['stage']]
            );

            $homeId = $this->findId(
                '{{%football_team}}',
                ['name' => $match['home']]
            );

            $awayId = $this->findId(
                '{{%football_team}}',
                ['name' => $match['away']]
            );

            if (
                $stageId !== null &&
                $homeId !== null &&
                $awayId !== null
            ) {
                $this->delete(
                    '{{%football_match}}',
                    [
                        'stage_id' => $stageId,
                        'home_team_id' => $homeId,
                        'away_team_id' => $awayId,
                        'kickoff_at' => $match['kickoff_at'],
                    ]
                );
            }
        }

        // 删除本迁移添加的场馆
        foreach ($this->stadiumDefinitions() as $stadium) {
            $cityId = $this->findId(
                '{{%host_city}}',
                [
                    'name' => $stadium['city_name'],
                    'country' => $stadium['country'],
                ]
            );

            if ($cityId !== null) {
                $this->delete(
                    '{{%stadium}}',
                    [
                        'name' => $stadium['name'],
                        'city_id' => $cityId,
                    ]
                );
            }
        }

        // 删除本迁移添加的球队
        foreach ($this->teamDefinitions() as $team) {
            $this->delete(
                '{{%football_team}}',
                ['name' => $team['name']]
            );
        }

        return true;
    }

    private function teamDefinitions()
    {
        return [
            ['name' => '北星队', 'country' => '示例国家A1', 'group_name' => 'A'],
            ['name' => '海岸队', 'country' => '示例国家A2', 'group_name' => 'A'],
            ['name' => '山城队', 'country' => '示例国家A3', 'group_name' => 'A'],
            ['name' => '森林队', 'country' => '示例国家A4', 'group_name' => 'A'],

            ['name' => '红枫队', 'country' => '示例国家B1', 'group_name' => 'B'],
            ['name' => '蓝湾队', 'country' => '示例国家B2', 'group_name' => 'B'],
            ['name' => '银鹰队', 'country' => '示例国家B3', 'group_name' => 'B'],
            ['name' => '金狮队', 'country' => '示例国家B4', 'group_name' => 'B'],
        ];
    }

    private function stadiumDefinitions()
    {
        return [
            [
                'name' => '亚特兰大演示球场',
                'city_name' => '亚特兰大',
                'country' => '美国',
                'capacity' => 71000,
                'address' => '亚特兰大，演示地址',
            ],
            [
                'name' => '达拉斯演示球场',
                'city_name' => '达拉斯',
                'country' => '美国',
                'capacity' => 80000,
                'address' => '达拉斯，演示地址',
            ],
            [
                'name' => '多伦多演示球场',
                'city_name' => '多伦多',
                'country' => '加拿大',
                'capacity' => 45000,
                'address' => '多伦多，演示地址',
            ],
            [
                'name' => '墨西哥城演示球场',
                'city_name' => '墨西哥城',
                'country' => '墨西哥',
                'capacity' => 83000,
                'address' => '墨西哥城，演示地址',
            ],
        ];
    }

    private function matchDefinitions()
    {
        return [
            // A 组：6 场演示完赛
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '北星队', 'away' => '海岸队',
                'kickoff_at' => '2026-06-11 18:00:00',
                'home_score' => 2, 'away_score' => 1,
                'status' => 'finished', 'stadium' => '亚特兰大演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '山城队', 'away' => '森林队',
                'kickoff_at' => '2026-06-12 18:00:00',
                'home_score' => 0, 'away_score' => 0,
                'status' => 'finished', 'stadium' => '达拉斯演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '北星队', 'away' => '山城队',
                'kickoff_at' => '2026-06-14 18:00:00',
                'home_score' => 1, 'away_score' => 1,
                'status' => 'finished', 'stadium' => '多伦多演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '海岸队', 'away' => '森林队',
                'kickoff_at' => '2026-06-14 21:00:00',
                'home_score' => 3, 'away_score' => 0,
                'status' => 'finished', 'stadium' => '墨西哥城演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '北星队', 'away' => '森林队',
                'kickoff_at' => '2026-06-17 18:00:00',
                'home_score' => 2, 'away_score' => 0,
                'status' => 'finished', 'stadium' => '亚特兰大演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'A',
                'home' => '海岸队', 'away' => '山城队',
                'kickoff_at' => '2026-06-17 21:00:00',
                'home_score' => 1, 'away_score' => 2,
                'status' => 'finished', 'stadium' => '达拉斯演示球场',
            ],

            // B 组：6 场演示完赛
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '红枫队', 'away' => '蓝湾队',
                'kickoff_at' => '2026-06-11 21:00:00',
                'home_score' => 1, 'away_score' => 0,
                'status' => 'finished', 'stadium' => '多伦多演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '银鹰队', 'away' => '金狮队',
                'kickoff_at' => '2026-06-12 21:00:00',
                'home_score' => 2, 'away_score' => 2,
                'status' => 'finished', 'stadium' => '墨西哥城演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '红枫队', 'away' => '银鹰队',
                'kickoff_at' => '2026-06-15 18:00:00',
                'home_score' => 0, 'away_score' => 1,
                'status' => 'finished', 'stadium' => '亚特兰大演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '蓝湾队', 'away' => '金狮队',
                'kickoff_at' => '2026-06-15 21:00:00',
                'home_score' => 2, 'away_score' => 1,
                'status' => 'finished', 'stadium' => '达拉斯演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '红枫队', 'away' => '金狮队',
                'kickoff_at' => '2026-06-18 18:00:00',
                'home_score' => 3, 'away_score' => 1,
                'status' => 'finished', 'stadium' => '多伦多演示球场',
            ],
            [
                'stage' => '小组赛', 'group_name' => 'B',
                'home' => '蓝湾队', 'away' => '银鹰队',
                'kickoff_at' => '2026-06-18 21:00:00',
                'home_score' => 0, 'away_score' => 0,
                'status' => 'finished', 'stadium' => '墨西哥城演示球场',
            ],

            // 两场未来的演示赛程，不代表官方赛程
            [
                'stage' => '32强赛', 'group_name' => null,
                'home' => '北星队', 'away' => '红枫队',
                'kickoff_at' => '2026-10-30 19:00:00',
                'home_score' => null, 'away_score' => null,
                'status' => 'scheduled', 'stadium' => '亚特兰大演示球场',
            ],
            [
                'stage' => '32强赛', 'group_name' => null,
                'home' => '海岸队', 'away' => '蓝湾队',
                'kickoff_at' => '2026-11-01 19:00:00',
                'home_score' => null, 'away_score' => null,
                'status' => 'scheduled', 'stadium' => '达拉斯演示球场',
            ],
        ];
    }

    private function findId($table, $condition)
    {
        $value = (new Query())
            ->select('id')
            ->from($table)
            ->where($condition)
            ->scalar($this->db);

        return ($value === false || $value === null)
            ? null
            : (int) $value;
    }

    private function insertIfMissing($table, $condition, $data)
    {
        $exists = (new Query())
            ->from($table)
            ->where($condition)
            ->exists($this->db);

        if (!$exists) {
            $this->insert($table, $data);
        }
    }
}
