<?php

use yii\helpers\Html;

$this->title = '球队信息';
?>

<div class="container-fluid">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        查看世界杯演示球队、所属小组和国家信息。
    </p>

    <?php if (empty($teams)): ?>
        <div class="alert alert-info">
            暂无球队数据，请先录入球队。
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>球队编号</th>
                        <th>球队名称</th>
                        <th>所属小组</th>
                        <th>国家或地区</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td>
                                <?= Html::encode($team['id']) ?>
                            </td>
                            <td>
                                <?= Html::encode($team['name']) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    $team['group_name'] ?? '未分组'
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode($team['country']) ?>
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
