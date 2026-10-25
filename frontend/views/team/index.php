<?php

use yii\helpers\Html;

$this->title = '球队信息';
?>

<div class="container-fluid">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        点击球队名称，查看球队详情及其参加的比赛。
    </p>

    <?php if (empty($teams)): ?>
        <div class="alert alert-info">
            暂无球队数据。
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>球队名称</th>
                        <th>所属小组</th>
                        <th>国家或地区</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td>
                                <?= Html::encode($team['id']) ?>
                            </td>
                            <td>
                                <?= Html::a(
                                    Html::encode($team['name']),
                                    ['/team/view', 'id' => $team['id']]
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    $team['group_name'] ?? '未分组'
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode($team['country']) ?>
                            </td>
                            <td>
                                <?= Html::a(
                                    '查看详情',
                                    ['/team/view', 'id' => $team['id']],
                                    ['class' => 'btn btn-primary btn-sm']
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="text-muted">
            共 <?= count($teams) ?> 支演示球队。
        </p>
    <?php endif; ?>
</div>
