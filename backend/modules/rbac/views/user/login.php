<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = '管理登录';

$fieldOptions1 = [
    'options' => ['class' => 'layui-form-item'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'layui-form-item'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div>
	<h1>后台登录</h1>
</div>
<div>
	<?php $form = ActiveForm::begin(['id' => 'login-form','options'=>['class' => 'layui-form'], 'enableClientValidation' => false]); ?>

	<?= $form
		->field($model, 'username', $fieldOptions1)
		->label(false)
		->textInput(['class' => 'layui-input','lay-verify'=>'required','placeholder' => $model->getAttributeLabel('username')]) ?>

	<?= $form
		->field($model, 'password', $fieldOptions2)
		->label(false)
		->passwordInput(['class' => 'layui-input','lay-verify'=>'required','placeholder' => $model->getAttributeLabel('password')]) ?>

	<div class="layui-form-item">
		<?= Html::submitButton("登录", ['class' => 'layui-btn login_btn','lay-submit'=>'','name' => 'login-button']) ?>
	</div>

	<!--<div>
		<div style="float: left;">
			<?/*= $form->field($model, 'rememberMe')->label(false)->checkbox(['class' => 'lay-ignore']) */?>
		</div>
	</div>-->
	<?php ActiveForm::end(); ?>
</div>