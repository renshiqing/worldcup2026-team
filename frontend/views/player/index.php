<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = '球员管理';
?>
<div class="container-fluid">
    <div class="page-header">
        <h1>球员管理</h1>
        <p class="text-muted">查看和维护世界杯演示球队的球员名单。</p>
    </div>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type === 'error' ? 'danger' : $type) ?>">
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['/player/index'], 'get', ['class' => 'form-inline']) ?>
                <div class="form-group">
                    <label for="player-team-filter">所属球队：</label>
                    <?= Html::dropDownList(
                        'team_id',
                        $teamId,
                        ArrayHelper::map($teams, 'id', 'name'),
                        ['id' => 'player-team-filter', 'prompt' => '全部球队', 'class' => 'form-control']
                    ) ?>
                </div>
                <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('重置', ['/player/index'], ['class' => 'btn btn-default']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('＋ 新增球员', ['create', 'team_id' => $teamId], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <span class="text-muted">
                登录后可以新增、编辑或删除球员。
                <?= Html::a('登录', ['/site/login']) ?> /
                <?= Html::a('注册', ['/site/signup']) ?>
            </span>
        <?php endif; ?>
    </p>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>球队</th><th>号码</th><th>姓名</th><th>位置</th>
                    <th>国籍/地区</th><th>出生日期</th><th>队长</th><th style="min-width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($players)): ?>
                    <tr><td colspan="8" class="text-center text-muted">暂无球员记录。登录后可通过“新增球员”添加演示数据。</td></tr>
                <?php else: ?>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?= Html::encode($player['team_name'] ?: '未知球队') ?></td>
                            <td><?= $player['shirt_number'] !== null ? Html::encode($player['shirt_number']) : '—' ?></td>
                            <td><?= Html::a(Html::encode($player['name']), ['view', 'id' => $player['id']]) ?></td>
                            <td><?= Html::encode($player['position'] ?: '未填写') ?></td>
                            <td><?= Html::encode($player['nationality'] ?: '未填写') ?></td>
                            <td><?= Html::encode($player['birth_date'] ?: '未填写') ?></td>
                            <td><?= !empty($player['is_captain']) ? '<span class="label label-warning">队长</span>' : '—' ?></td>
                            <td>
                                <?= Html::a('详情', ['view', 'id' => $player['id']], ['class' => 'btn btn-xs btn-default']) ?>
                                <?php if (!Yii::$app->user->isGuest): ?>
                                    <?= Html::a('编辑', ['update', 'id' => $player['id']], ['class' => 'btn btn-xs btn-primary']) ?>
                                    <?= Html::a('删除', ['delete', 'id' => $player['id']], [
                                        'class' => 'btn btn-xs btn-danger',
                                        'data-method' => 'post',
                                        'data-confirm' => '确定删除这名球员吗？此操作不可撤销。',
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted">当前共 <?= count($players) ?> 条球员记录。球队及球员数据为课程项目演示数据。</p>
</div>
