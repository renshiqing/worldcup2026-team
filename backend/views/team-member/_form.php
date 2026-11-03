<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team-member/_form.php
 * 功能：团队成员管理表单组件
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamMember */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="team-member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'student_id')->textInput(['maxlength' => true])->hint('例如：20230001') ?>

    <?= $form->field($model, 'role')->textInput(['maxlength' => true])->hint('例如：组长 / 数据库与比赛 / 前台 / 后台与统计') ?>

    <?= $form->field($model, 'display_order')->textInput(['type' => 'number', 'min' => 0])->hint('数字越小越靠前') ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true])->hint('头像图片地址，可留空') ?>

    <?= $form->field($model, 'bio')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增成员' : '保存修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
