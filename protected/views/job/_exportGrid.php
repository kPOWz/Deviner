<?php
echo CHtml::ajaxLink('Export Checked Jobs'
							,CHtml::normalizeUrl('job/export') 
							,array(
									//'dataType'      => 'json',
									'type'=>'POST',
									//'export_begin=' . 'js:\$("#export_begin").val();' . '&export_end=' . 'js:\$("#export_end").val()' . '&export_status=' . 'js:\$("#export_status").val()'
									//'data' => "js:{ids:$.fn.yiiGridView.getChecked('export_grid', 'checked')}"
//'js:{theIds : $.fn.yiiGridView.getChecked("example-grid-view-id","example-check-boxes").toString()}'
        
									'data' => 'js:{ids : $.fn.yiiGridView.getChecked("export_grid", "checked").toString()}'			)
							,array('id'=>'export_submit', 'name'=>'export_submit','class' =>'fixed-medium vert-align')
		
		); 
?>
<!-- </div>  -->
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
					'class' => 'CCheckBoxColumn',
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
