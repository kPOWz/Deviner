<div id="export_form_container" class="form">

<!-- why does he have ajax validation off everywhere ?-->
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'export_preview_form',
	'method'=>'GET',
	'clientOptions'=>array('validateOnSubmit'=>false, 'validateOnType'=>false)
)); ?>

<fieldset >
<div class='grid_9' >
<div class='grid_6' >
<h3>Export jobs printed </h3>
<b>from :</b>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'export_begin',
	'id'=>'export_begin',
     'options'=>array(
        'showAnim'=>'fold',
        'dateFormat'=>'yy-mm-dd',
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
?>


<b>to :</b>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'export_end',
	'id'=>'export_end',
     'options'=>array(
        'showAnim'=>'fold',
        'dateFormat'=>'yy-mm-dd',
 
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
?>
	
<!--TODO make this a multi-select? -->	
<b>with status :</b>	
<?php 
$statuses = CHtml::listData(Lookup::listItems('JobStatus'), 'ID', 'TEXT');
echo CHtml::dropDownList('export_status', array_search('Completed', $statuses), $statuses, array('id'=>'export_status'))
?>
</div>
             
<?php echo CHtml::ajaxButton('Preview Export'
							,CHtml::normalizeUrl('job/export') 
							,array(
									'update'        => "#export_preview_results",
									'dataType'      => 'html',
									'type'			=> 'GET',
									//data not needed as this is automatically handled by the CHtml ajaxButton 
									//'data'			=> 'export_begin=' . 'js:\$("#export_begin").val();' . '&export_end=' . 'js:\$("#export_end").val()' . '&export_status=' . 'js:\$("#export_status").val()'
							)
							,array('id'=>'export_preview_submit', 'name'=>'export_preview_submit','class' =>'fixed-medium')
		
		); ?>
</div>
</fieldset>
<?php $this->endWidget(); ?>



</div><!-- form -->

<!--  do a grid view w/ check box column where all checked -->
<div id='export_preview_results'> <!--  class='form'> -->
</div>