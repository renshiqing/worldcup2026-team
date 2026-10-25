<?php
use yii\helpers\Html;

$this->title = $model->name . ' - 球员详情';
?>
<div class="container-fluid">
    <p><?= Html::a('← 返回球员列表', ['/player/index', 'team_id' => $model->team_id]) ?></p>

    <div class="panel panel-primary">
        <div class="panel-heading"><h2 class="panel-title"><?= Html::encode($model->name) ?></h2></div>
        <div class="panel-body">
            <dl class="dl-horizontal">
                <dt>球员编号</dt><dd><?= Html::encode($model->id) ?></dd>
                <dt>所属球队</dt>
                <dd>
                    <?php if ($team): ?>
                        <?= Html::a(Html::encode($team['name']), ['/team/view', 'id' => $team['id']]) ?>
                    <?php else: ?>未知球队<?php endif; ?>
                </dd>
                <dt>国籍/地区</dt><dd><?= Html::encode($model->nationality ?: '未填写') ?></dd>
                <dt>球衣号码</dt><dd><?= $model->shirt_number !== null ? Html::encode($model->shirt_number) : '未填写' ?></dd>
                <dt>场上位置</dt><dd><?= Html::encode($model->position ?: '未填写') ?></dd>
                <dt>出生日期</dt><dd><?= Html::encode($model->birth_date ?: '未填写') ?></dd>
                <dt>队长</dt><dd><?= $model->is_captain ? '是' : '否' ?></dd>
            </dl>
            <?php if (!Yii::$app->user->isGuest): ?>
                <p>
                    <?= Html::a('编辑球员', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('删除球员', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data-method' => 'post',
                        'data-confirm' => '确定删除这名球员吗？此操作不可撤销。',
                    ]) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
