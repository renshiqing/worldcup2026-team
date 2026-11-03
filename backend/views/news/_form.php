<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/news/_form.php
 * 功能：新闻管理表单组件
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\NewsArticle */
/* @var $statusOptions array */
/* @var $categoryOptions array */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category')->dropDownList($categoryOptions) ?>

    <?= $form->field($model, 'summary')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 12]) ?>

    <?= $form->field($model, 'status')->dropDownList($statusOptions)->hint('选择“已发布”后，系统会自动填写发布时间；撤回为草稿后前台不再显示。') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存新闻' : '保存修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
