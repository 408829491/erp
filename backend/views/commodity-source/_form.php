<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CommoditySource */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="commodity-source-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inspect_date')->textInput() ?>

    <?= $form->field($model, 'inspect_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inspect_man')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inspect_organization')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'provider_id')->textInput() ?>

    <?= $form->field($model, 'provider_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_delete')->textInput() ?>

    <?= $form->field($model, 'trace_report_arr')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'modify_time')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
