<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\TeamPlayer $model */
/** @var array $teams */
?>
<div class="player-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'team_id')->dropDownList($teams, ['prompt' => '请选择球队']) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入球员姓名']) ?>
    <?= $form->field($model, 'shirt_number')->textInput([
        'type' => 'number', 'min' => 0, 'max' => 65535, 'placeholder' => '可留空',
    ]) ?>
    <?= $form->field($model, 'position')->dropDownList([
        '守门员' => '守门员',
        '后卫' => '后卫',
        '中场' => '中场',
        '前锋' => '前锋',
    ], ['prompt' => '请选择位置']) ?>
    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true, 'placeholder' => '例如：示例国家A1']) ?>
    <?= $form->field($model, 'birth_date')->textInput(['type' => 'date']) ?>
    <?= $form->field($model, 'is_captain')->checkbox(['label' => '设为球队队长']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加球员' : '保存修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index', 'team_id' => $model->team_id], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
