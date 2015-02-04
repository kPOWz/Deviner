<?php
	$this->layout = 'default';
	$this->pageTitle=Yii::app()->name . ' - Login';
	$this->breadcrumbs=array(
		'Login',
	);
?>

<h1>GUS</h1>

<!-- comments above the login form indicate we may want to use a BS Justified button group...
<!-- class="btn-group btn-group-justified" -->
<!-- <a class="btn btn-default" role="button">Middle</a> -->
<!-- <div class="btn-group">
<button type="button" class="btn btn-default">Middle</button>
</div> -->
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<?php echo $form->textField($model,'username', array('placeholder'=>'email*',)); ?>
	<?php echo $form->error($model,'username'); ?>

	<?php echo $form->passwordField($model,'password', array('placeholder'=>'password*',)); ?>
	<?php echo $form->error($model,'password'); ?>

	<?php echo CHtml::submitButton('login'); ?>

	<?php echo $form->checkBox($model,'rememberMe'); ?>
	<?php echo $form->label($model,'rememberMe'); ?>
	<?php echo $form->error($model,'rememberMe'); ?>
<?php $this->endWidget(); ?>
