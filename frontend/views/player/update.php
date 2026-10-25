<?php
use yii\helpers\Html;

$this->title = '编辑球员：' . $model->name;
?>
<div class="container-fluid">
    <h1>编辑球员</h1>
    <p class="text-muted"><?= Html::encode($model->name) ?></p>
    <?= $this->render('_form', ['model' => $model, 'teams' => $teams]) ?>
</div>
