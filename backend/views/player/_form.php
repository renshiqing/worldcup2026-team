<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/player/_form.php
 * 功能：球员管理表单组件
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */
/* @var $teamOptions array */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="player-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'team_id')->dropDownList($teamOptions, ['prompt' => '请选择球队']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shirt_number')->textInput(['type' => 'number', 'min' => 0, 'max' => 999]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true])->hint('例如：前锋 / 中场 / 后卫 / 门将') ?>

    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birth_date')->input('date') ?>

    <?= $form->field($model, 'is_captain')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增球员' : '保存修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
