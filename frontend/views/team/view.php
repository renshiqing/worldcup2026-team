<?php

use yii\helpers\Html;

$this->title = $team['name'] . ' - 球队详情';

$statusLabels = [
    'scheduled' => '待开赛',
    'finished' => '已结束',
    'in_progress' => '进行中',
    'live' => '进行中',
    'postponed' => '已延期',
    'cancelled' => '已取消',
];
?>

<div class="container-fluid">

    <p>
        <?= Html::a('← 返回球队列表', ['/team/index']) ?>
    </p>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title">
                <?= Html::encode($team['name']) ?>
            </h2>
        </div>

        <div class="panel-body">
            <p>
                <strong>球队编号：</strong>
                <?= Html::encode($team['id']) ?>
            </p>

            <p>
                <strong>国家或地区：</strong>
                <?= Html::encode($team['country']) ?>
            </p>

            <p>
                <strong>所属小组：</strong>
                <?= Html::encode(
                    $team['group_name'] ?? '未分组'
                ) ?>
            </p>
        </div>
    </div>

    <h2>比赛记录</h2>

    <p class="text-muted">
        以下为该球队作为主队或客队参加的全部演示比赛，
        按比赛时间排序。
    </p>

    <?php if (empty($matches)): ?>

        <div class="alert alert-info">
            该球队暂时没有比赛记录。
        </div>

    <?php else: ?>

        <p>
            共找到
            <strong><?= count($matches) ?></strong>
            场相关比赛。
        </p>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>比赛时间</th>
                        <th>阶段 / 小组</th>
                        <th>主队</th>
                        <th>比分</th>
                        <th>客队</th>
                        <th>本队角色</th>
                        <th>场馆</th>
                        <th>状态</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($matches as $match): ?>

                        <?php
                        $isHome = (int)$match['home_team_id']
                            === (int)$team['id'];

                        $stageText = $match['stage_name']
                            ?: '未分类';

                        if (!empty($match['group_name'])) {
                            $stageText .= ' / '
                                . $match['group_name'] . '组';
                        }

                        $hasScore =
                            $match['home_score'] !== null
                            && $match['away_score'] !== null;

                        $score = $hasScore
                            ? $match['home_score']
                                . ' : '
                                . $match['away_score']
                            : '—';

                        $location = $match['stadium_name'] ?? '';

                        if (!empty($match['city_name'])) {
                            $location .= ' / ' . $match['city_name'];
                        }

                        $statusText = $statusLabels[
                            $match['status']
                        ] ?? $match['status'];
                        ?>

                        <tr>
                            <td>
                                <?= Html::encode(
                                    $match['kickoff_at']
                                ) ?>
                            </td>

                            <td>
                                <?= Html::encode($stageText) ?>
                            </td>

                            <td>
                                <?= Html::encode(
                                    $match['home_team_name']
                                    ?? '待定'
                                ) ?>
                            </td>

                            <td>
                                <strong>
                                    <?= Html::encode($score) ?>
                                </strong>
                            </td>

                            <td>
                                <?= Html::encode(
                                    $match['away_team_name']
                                    ?? '待定'
                                ) ?>
                            </td>

                            <td>
                                <?= $isHome ? '主队' : '客队' ?>
                            </td>

                            <td>
                                <?= Html::encode(
                                    $location ?: '未安排'
                                ) ?>
                            </td>

                            <td>
                                <?= Html::encode($statusText) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <hr>

    <p class="text-muted">
        本页面为课程项目，比赛及比分均为演示数据，
        不代表官方赛事信息。
    </p>

</div>
