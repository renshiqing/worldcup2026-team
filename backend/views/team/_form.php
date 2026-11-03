<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team/_form.php
 * 功能：球队管理表单组件
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\FootballTeam */
/* @var $groupOptions array */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->dropDownList($groupOptions, ['prompt' => '未分组']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增球队' : '保存修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
