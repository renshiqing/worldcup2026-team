<?php

use yii\helpers\Html;

$this->title = '世界杯比赛列表（演示数据）';

$statusLabels = [
    'finished' => '已结束',
    'scheduled' => '待开赛',
    'live' => '进行中',
    'postponed' => '已延期',
    'cancelled' => '已取消',
];
?>

<div class="match-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        当前页面使用本地演示数据，不代表官方赛程及比赛结果。
    </p>
  <!-- 比赛筛选栏 -->
<div class="panel panel-default">
    <div class="panel-body">

        <?= Html::beginForm(
            ['match/index'],
            'get',
            ['class' => 'form-inline']
        ) ?>

        <div class="form-group" style="margin-right: 12px;">
            <?= Html::label(
                '比赛小组：',
                'group',
                ['style' => 'margin-right: 6px;']
            ) ?>

            <?= Html::dropDownList(
                'group',
                $selectedGroup,
                $groupOptions,
                [
                    'id' => 'group',
                    'class' => 'form-control',
                ]
            ) ?>
        </div>

        <div class="form-group" style="margin-right: 12px;">
            <?= Html::label(
                '比赛状态：',
                'status',
                ['style' => 'margin-right: 6px;']
            ) ?>

            <?= Html::dropDownList(
                'status',
                $selectedStatus,
                $statusOptions,
                [
                    'id' => 'status',
                    'class' => 'form-control',
                ]
            ) ?>
        </div>

        <?= Html::submitButton(
            '筛选比赛',
            ['class' => 'btn btn-success']
        ) ?>

        <?= Html::a(
            '重置',
            ['match/index'],
            [
                'class' => 'btn btn-default',
                'style' => 'margin-left: 6px;',
            ]
        ) ?>

        <?= Html::endForm() ?>

    </div>
</div>

<p class="text-muted">
    当前筛选结果：<?= count($matches) ?> 场比赛
</p>
    <p>
        <?= Html::a(
            '查看小组积分榜',
            ['match/standings'],
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <?php if (empty($matches)): ?>
        
        <div class="alert alert-info">
    没有符合当前筛选条件的比赛，请调整筛选条件或点击“重置”。
</div>
    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>比赛时间</th>
                        <th>阶段 / 小组</th>
                        <th>主队</th>
                        <th>比分</th>
                        <th>客队</th>
                        <th>场馆</th>
                        <th>状态</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <?php
                        $hasScore =
                            $match['home_score'] !== null
                            && $match['away_score'] !== null;

                        $score = $hasScore
                            ? $match['home_score']
                                . ' : '
                                . $match['away_score']
                            : '—';

                        $venue = $match['stadium_name'] ?: '待定';

                        if (!empty($match['city_name'])) {
                            $venue .= ' / ' . $match['city_name'];
                        }

                        $status = $statusLabels[$match['status']]
                            ?? $match['status'];
                        ?>

                        <tr>
                            <td>
                                <?= Html::encode(
                                    date(
                                        'Y-m-d H:i',
                                        strtotime($match['kickoff_at'])
                                    )
                                ) ?>
                            </td>

                            <td>
                                <?= Html::encode($match['stage_name']) ?>
                                <?php if ($match['group_name'] !== null): ?>
                                    <br>
                                    <small>
                                        <?= Html::encode(
                                            $match['group_name'] . ' 组'
                                        ) ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                           
 			    <td>
            <?= Html::a(
        Html::encode($match['home_team']),
        ['/team/view', 'id' => $match['home_team_id']],
        ['title' => '查看主队详情']
    ) ?>
                           </td>
                            <td>
                                <strong>
                                    <?= Html::encode($score) ?>
                                </strong>
                            </td>

                            
			    <td>
    <?= Html::a(
        Html::encode($match['away_team']),
        ['/team/view', 'id' => $match['away_team_id']],
        ['title' => '查看客队详情']
    ) ?>
			   </td>
                            <td>
                                <?= Html::encode($venue) ?>
                            </td>

                            <td>
                                <?= Html::encode($status) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>
