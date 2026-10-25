<?php

use yii\helpers\Html;

$this->title = '比赛详情';

$this->params['breadcrumbs'][] = [
    'label' => '比赛列表',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;

$statusLabels = [
    'finished' => '已结束',
    'scheduled' => '待开赛',
    'live' => '进行中',
    'postponed' => '已延期',
    'cancelled' => '已取消',
];

$hasScore = $match['home_score'] !== null
    && $match['away_score'] !== null;

$score = $hasScore
    ? $match['home_score'] . ' : ' . $match['away_score']
    : '—';

$status = $statusLabels[$match['status']]
    ?? $match['status'];

$stage = $match['stage_name'];

if (!empty($match['group_name'])) {
    $stage .= ' / ' . $match['group_name'] . ' 组';
}

$venue = $match['stadium_name'] ?: '暂未安排';

if (!empty($match['city_name'])) {
    $venue .= ' / ' . $match['city_name'];

    if (!empty($match['city_country'])) {
        $venue .= '，' . $match['city_country'];
    }
}
?>

<div class="match-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        本页面展示课程项目中的比赛演示数据，
        不代表官方赛程或比赛结果。
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Html::encode($stage) ?>
        </div>

        <div class="panel-body">
            <p>
                <strong>比赛编号：</strong>
                <?= (int)$match['id'] ?>
            </p>

            <p>
                <strong>比赛时间：</strong>
                <?= Html::encode($match['kickoff_at']) ?>
            </p>

            <hr>

            <div class="row text-center">
                <div class="col-xs-4">
                    <h3>
                        <?= Html::a(
                            Html::encode($match['home_team_name']),
                            ['/team/view', 'id' => $match['home_team_id']]
                        ) ?>
                    </h3>
                    <p>
                        <?= Html::encode($match['home_country']) ?>
                    </p>
                </div>

                <div class="col-xs-4">
                    <h2>
                        <?= Html::encode($score) ?>
                    </h2>
                    <p>
                        <?= Html::encode($status) ?>
                    </p>
                </div>

                <div class="col-xs-4">
                    <h3>
                        <?= Html::a(
                            Html::encode($match['away_team_name']),
                            ['/team/view', 'id' => $match['away_team_id']]
                        ) ?>
                    </h3>
                    <p>
                        <?= Html::encode($match['away_country']) ?>
                    </p>
                </div>
            </div>

            <hr>

            <p>
                <strong>比赛阶段：</strong>
                <?= Html::encode($stage) ?>
            </p>

            <p>
                <strong>比赛场馆：</strong>
                <?= Html::encode($venue) ?>
            </p>

            <p>
                <strong>比赛状态：</strong>
                <?= Html::encode($status) ?>
            </p>

            <p>
                <strong>比赛结果：</strong>
                <?php if ($hasScore): ?>
                    已记录比分
                <?php else: ?>
                    暂无完整比分
                <?php endif; ?>
            </p>
        </div>
    </div>

    <p>
        <?= Html::a(
            '返回比赛列表',
            ['index'],
            ['class' => 'btn btn-default']
        ) ?>
    </p>
</div>
