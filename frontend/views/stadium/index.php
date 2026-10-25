<?php

use yii\helpers\Html;

$this->title = '场馆信息';
?>

<div class="container-fluid">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        查看世界杯演示场馆的所在城市、国家和容量。
    </p>

    <?php if (empty($stadiums)): ?>
        <div class="alert alert-info">
            暂无场馆数据，请先录入场馆。
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>场馆编号</th>
                        <th>场馆名称</th>
                        <th>所在城市</th>
                        <th>国家</th>
                        <th>容量</th>
                        <th>地址</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stadiums as $stadium): ?>
                        <tr>
                            <td>
                                <?= Html::encode($stadium['id']) ?>
                            </td>
                            <td>
                                <?= Html::encode($stadium['name']) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    $stadium['city_name'] ?? '未填写'
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    $stadium['country'] ?? '未填写'
                                ) ?>
                            </td>
                            <td>
                                <?php if ($stadium['capacity'] !== null): ?>
                                    <?= number_format(
                                        (int)$stadium['capacity']
                                    ) ?> 人
                                <?php else: ?>
                                    未填写
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    $stadium['address'] ?: '未填写'
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="text-muted">
            共 <?= count($stadiums) ?> 个演示场馆。
        </p>
    <?php endif; ?>
</div>
