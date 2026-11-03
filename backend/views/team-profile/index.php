<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/views/team-profile/index.php
 * 功能：团队资料编辑页面
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

/* @var $this yii\web\View */
/* @var $model common\models\TeamProfile */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = '团队资料';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        这里维护课程项目团队的基本资料，前台团队主页会读取这些内容。
        修改后点击“保存团队资料”即可。
    </p>

    <div class="row">
        <div class="col-md-8">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'team_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'slogan')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact_email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

            <div class="form-group">
                <?= Html::submitButton('保存团队资料', ['class' => 'btn btn-success']) ?>
                <?= Html::a('返回统计概览', ['/site/index'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">对应课程要求</div>
                <div class="panel-body">
                    <p>团队主页需要包含团队和个人展示，后台需要能编辑团队/个人资料。</p>
                    <p>团队成员在<?= Html::a('成员管理', ['/team-member/index']) ?>中维护。</p>
                </div>
            </div>
        </div>
    </div>

</div>
