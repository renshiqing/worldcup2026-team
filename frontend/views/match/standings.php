
<?php

use yii\helpers\Html;

$this->title = '世界杯小组积分榜（演示数据）';
?>

<div class="match-standings">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        仅统计状态为“已结束”且双方比分完整的小组赛。
        胜 3 分，平 1 分，负 0 分。
    </p>

    <p>
        <?= Html::a(
            '返回比赛列表',
            ['match/index'],
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <?php if (empty($standings)): ?>
        <div class="alert alert-info">
            暂无积分榜数据，请先录入球队和已结束的小组赛。
        </div>
    <?php else: ?>

        <?php foreach ($standings as $group): ?>
            <h2><?= Html::encode($group['group_name']) ?> 组</h2>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>排名</th>
                            <th>球队</th>
                            <th>场次</th>
                            <th>胜</th>
                            <th>平</th>
                            <th>负</th>
                            <th>进球</th>
                            <th>失球</th>
                            <th>净胜球</th>
                            <th>积分</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (
                            $group['teams'] as $index => $team
                        ): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?= Html::encode($team['name']) ?>
                                </td>

                                <td><?= $team['played'] ?></td>
                                <td><?= $team['wins'] ?></td>
                                <td><?= $team['draws'] ?></td>
                                <td><?= $team['losses'] ?></td>
                                <td><?= $team['goals_for'] ?></td>
                                <td><?= $team['goals_against'] ?></td>
                                <td>
                                    <?= $team['goal_difference'] > 0
                                        ? '+'
                                        : '' ?>
                                    <?= $team['goal_difference'] ?>
                                </td>
                                <td>
                                    <strong><?= $team['points'] ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>
