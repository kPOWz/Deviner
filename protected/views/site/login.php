<?php
	$this->layout = 'default';
	$this->pageTitle=Yii::app()->name . ' - Login';
	$this->breadcrumbs=array(
		'Login',
	);
?>



<main class="row center-block-vertical">
<h1 class="text-center text-primary" id='gus-identity'>GUS</h1>
<!-- comments above the login form indicate we may want to use a BS Justified button group...
<!-- class="btn-group btn-group-justified" -->
<!-- <a class="btn btn-default" role="button">Middle</a> -->
<!-- <div class="btn-group">
<button type="button" class="btn btn-default">Middle</button>
</div> -->
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'enableAjaxValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
		'errorCssClass'=>'has-error',
	),
	'htmlOptions'=>array(
		'class'=>'col-md-4 center-block container-fluid'
	),
	'errorMessageCssClass'=>'control-label',
)); ?>

	<div class="row form-group gus-input-group">
		<?php echo $form->emailField($model,'username', array('placeholder'=>'email*', 'class'=>'col-md-12 form-control input-lg')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>
	<div class="row form-group gus-input-group">
		<?php echo $form->passwordField($model,'password', array('placeholder'=>'password*', 'class'=>'col-md-12 form-control input-lg')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
	<div class="row form-group gus-input-group">
		<?php echo CHtml::submitButton('login', array('class'=>'btn text-center col-md-12 btn-lg btn-inverse text-primary gus-btn')); ?>
	</div>
	<div class="row checkbox">
		<span class="pull-right">
		<?php echo $form->checkBox($model,'rememberMe', array('id'=>'cb-remember-me')); ?>
		<label id="cb-remember-me-custom" for="cb-remember-me" aria-checked="false" role='checkbox' tabindex="0" ></label>		
		<?php echo $form->label($model,'rememberMe', array('for'=>'cb-remember-me-custom', 'class'=>'text-muted')); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
		</span>
	</div>
<?php $this->endWidget(); ?>
</main>
