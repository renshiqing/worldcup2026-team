<?php
$this->title = '新增球员';
?>
<div class="container-fluid">
    <h1>新增球员</h1>
    <p class="text-muted">请填写球员基本信息。同一球队内的球衣号码不能重复。</p>
    <?= $this->render('_form', ['model' => $model, 'teams' => $teams]) ?>
</div>
