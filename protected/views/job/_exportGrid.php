
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'export_form',
	'clientOptions'=>array('validateOnSubmit'=>false, 'validateOnType'=>false)
)); ?>

<fieldset>
<div class='grid_9'>
	<div class='grid_6'>
		<h3>Jobs to export</h3>
	</div>

<?php echo CHtml::ajaxSubmitButton('Export Checked Jobs'
							,CHtml::normalizeUrl('job/export') 
							,array(
									//'dataType'      => 'script',
									//'export_begin=' . 'js:\$("#export_begin").val();' . '&export_end=' . 'js:\$("#export_end").val()' . '&export_status=' . 'js:\$("#export_status").val()'
										
									'data' => "js:{ids:$.fn.yiiGridView.getSelection('export_grid')}"			)
							,array('id'=>'export_submit', 'name'=>'export_submit','class' =>'fixed-medium vert-align')
		
		); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array( 
	'dataProvider'=>$exportData,
	'id'=>$gridId,
	'selectableRows' => 2,
	'nullDisplay'=> 'No jobs matching preview critera',
	'formatter'=>new Formatter,
	'columns'=>array(
			array(
					//'header' => 'Export?',
					'id' => 'checked',
					'class' => 'CheckedCheckBoxColumn',
					'checked' => 'true'
					//'value' => 'CHtml::checkBox("cid[]",true,array("value"=>$data->ID,"id"=>"cid_".$data->ID))',
					//'type'=>'raw',
					//'selectableRows'=>2,
			),
		array(
			'header' => 'Name',
			'value' => "\$data->NAME"
				
		),
		array(
				'header' => 'Status',
				'value' => "\$data->STATUS"
		
		),
		array(
				'header' => 'Date',
				'value' => "substr(\$data->events['11']->TIMESTAMP, 0, strpos( \$data->events['11']->TIMESTAMP, ' ' ) )"
		
		)
	)
)); ?>
</fieldset>
<?php $this->endWidget(); ?>