
<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Seeds reference data for the 2026 World Cup project.
 */
class m261025_025908_seed_worldcup_reference_data extends Migration
{
    /**
     * Insert reference data.
     */
    public function up()
    {
        $now = time();

        // 1. 创建 A-L 共 12 个小组
        foreach (range('A', 'L') as $groupName) {
            $this->insertIfMissing(
                '{{%tournament_group}}',
                ['group_name' => $groupName],
                [
                    'group_name' => $groupName,
                    'created_at' => $now,
                ]
            );
        }

        // 2. 创建比赛阶段
        $stages = [
            ['小组赛', 1],
            ['32强赛', 2],
            ['16强赛', 3],
            ['四分之一决赛', 4],
            ['半决赛', 5],
            ['季军赛', 6],
            ['决赛', 7],
        ];

        foreach ($stages as $stage) {
            $this->insertIfMissing(
                '{{%match_stage}}',
                ['stage_name' => $stage[0]],
                [
                    'stage_name' => $stage[0],
                    'stage_order' => $stage[1],
                    'created_at' => $now,
                ]
            );
        }

        // 3. 创建 16 个主办城市
        // 城市名称采用中文，country 使用中文国家名称。
        $cities = [
            ['亚特兰大', '美国', '美国佐治亚州主办城市'],
            ['波士顿', '美国', '比赛场馆位于马萨诸塞州福克斯伯勒'],
            ['达拉斯', '美国', '比赛场馆位于德克萨斯州阿灵顿'],
            ['休斯敦', '美国', '美国德克萨斯州主办城市'],
            ['堪萨斯城', '美国', '美国密苏里州主办城市'],
            ['洛杉矶', '美国', '比赛场馆位于加利福尼亚州英格尔伍德'],
            ['迈阿密', '美国', '美国佛罗里达州主办城市'],
            ['纽约/新泽西', '美国', '比赛场馆位于新泽西州东卢瑟福'],
            ['费城', '美国', '美国宾夕法尼亚州主办城市'],
            ['旧金山湾区', '美国', '比赛场馆位于加利福尼亚州圣克拉拉'],
            ['西雅图', '美国', '美国华盛顿州主办城市'],
            ['墨西哥城', '墨西哥', '墨西哥主办城市'],
            ['瓜达拉哈拉', '墨西哥', '墨西哥主办城市'],
            ['蒙特雷', '墨西哥', '墨西哥主办城市'],
            ['多伦多', '加拿大', '加拿大主办城市'],
            ['温哥华', '加拿大', '加拿大主办城市'],
        ];

        foreach ($cities as $city) {
            $this->insertIfMissing(
                '{{%host_city}}',
                [
                    'name' => $city[0],
                    'country' => $city[1],
                ],
                [
                    'name' => $city[0],
                    'country' => $city[1],
                    'description' => $city[2],
                    'created_at' => $now,
                ]
            );
        }

        return true;
    }

    /**
     * Remove the reference data inserted by this migration.
     */
    public function down()
    {
        $cities = [
            ['亚特兰大', '美国'],
            ['波士顿', '美国'],
            ['达拉斯', '美国'],
            ['休斯敦', '美国'],
            ['堪萨斯城', '美国'],
            ['洛杉矶', '美国'],
            ['迈阿密', '美国'],
            ['纽约/新泽西', '美国'],
            ['费城', '美国'],
            ['旧金山湾区', '美国'],
            ['西雅图', '美国'],
            ['墨西哥城', '墨西哥'],
            ['瓜达拉哈拉', '墨西哥'],
            ['蒙特雷', '墨西哥'],
            ['多伦多', '加拿大'],
            ['温哥华', '加拿大'],
        ];

        // 先删除城市，再删除比赛阶段和小组。
        foreach ($cities as $city) {
            $this->delete(
                '{{%host_city}}',
                ['name' => $city[0], 'country' => $city[1]]
            );
        }

        $stages = [
            '小组赛',
            '32强赛',
            '16强赛',
            '四分之一决赛',
            '半决赛',
            '季军赛',
            '决赛',
        ];

        foreach ($stages as $stageName) {
            $this->delete(
                '{{%match_stage}}',
                ['stage_name' => $stageName]
            );
        }

        foreach (range('A', 'L') as $groupName) {
            $this->delete(
                '{{%tournament_group}}',
                ['group_name' => $groupName]
            );
        }

        return true;
    }

    /**
     * Insert a row only when its unique identifying fields are absent.
     */
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
