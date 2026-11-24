<?php
/**
 * Update demo teams to real national team names.
 *
 * @author 任诗清
 */

use yii\db\Migration;

/**
 * Class m261124_133000_update_demo_team_names
 */
class m261124_133000_update_demo_team_names extends Migration
{
    /**
     * 将8支演示球队更新为真实国家队名称。
     */
    public function safeUp()
    {
        $teams = [
            1 => ['德国队', '德国'],
            2 => ['法国队', '法国'],
            3 => ['美国队', '美国'],
            4 => ['日本队', '日本'],
            5 => ['巴西队', '巴西'],
            6 => ['阿根廷队', '阿根廷'],
            7 => ['英格兰队', '英格兰'],
            8 => ['西班牙队', '西班牙'],
        ];

        foreach ($teams as $id => $team) {
            $this->update(
                '{{%football_team}}',
                [
                    'name' => $team[0],
                    'country' => $team[1],
                ],
                ['id' => $id]
            );
        }
    }

    /**
     * 回滚时恢复原来的演示球队名称。
     */
    public function safeDown()
    {
        $teams = [
            1 => ['北星队', '示例国家A1'],
            2 => ['海岸队', '示例国家A2'],
            3 => ['山城队', '示例国家A3'],
            4 => ['森林队', '示例国家A4'],
            5 => ['红枫队', '示例国家B1'],
            6 => ['蓝湾队', '示例国家B2'],
            7 => ['银鹰队', '示例国家B3'],
            8 => ['金狮队', '示例国家B4'],
        ];

        foreach ($teams as $id => $team) {
            $this->update(
                '{{%football_team}}',
                [
                    'name' => $team[0],
                    'country' => $team[1],
                ],
                ['id' => $id]
            );
        }
    }
}