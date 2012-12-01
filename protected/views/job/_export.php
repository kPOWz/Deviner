
<div class="form">

<!--onsubmit interception (part of html options :  'onsubmit'=>"$('#line_prototype').remove(); return true;"-->
<!--add an id ?-->
<!-- why does he have ajax validation off everywhere ?-->
<!--action & method needed defined explicity? -->
<!-- on submit do jobs filter, attach behaviors, do file, remove behaviors -->
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'id'=>'export-form',
	'method'=>'GET',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
	),
)); ?>

<div class="row" >
<b>From :</b>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'export_begin',  // name of post parameter
  //  'value'=>Yii::app()->request->cookies['export_begin']->value,  // value comes from cookie after submittion
     'options'=>array(
        'showAnim'=>'fold',
        'dateFormat'=>'yy-mm-dd',
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
?>


<b>To :</b>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'export_end',
    //'value'=>Yii::app()->request->cookies['end_date']->value,
     'options'=>array(
        'showAnim'=>'fold',
        'dateFormat'=>'yy-mm-dd',
 
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
?>
	
<!-- make this a multi-select? -->		
<?php 
$statuses = CHtml::listData(Lookup::listItems('JobStatus'), 'ID', 'TEXT');
echo CHtml::dropDownList('export_status', array_search('Completed', $statuses), $statuses)
?>
<?php echo CHtml::button('Export', array('submit' => array('job/export'))); ?>
</div>
<?php $this->endWidget(); ?>



</div><!-- form -->

<!--  do a grid view w/ check box column where all checked -->