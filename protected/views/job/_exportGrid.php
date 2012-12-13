<?php $this->widget('zii.widgets.grid.CGridView', array( 
	'dataProvider'=>$exportData,
	'formatter'=>new Formatter,
	'columns'=>array(
			array(
					'header' => 'EXPORT?',
					'value' => 'CHtml::checkBox("cid[]",null,array("value"=>$data->ID,"id"=>"cid_".$data->ID))',
					'type'=>'raw'
			),
		array(
			'header' => 'NAME',
			'value' => "\$data->NAME"
				
		),
		array(
				'header' => 'STATUS',
				'value' => "\$data->STATUS"
		
		),
		array(
				'header' => 'DATE',
				'value' => "substr(\$data->events['11']->TIMESTAMP, 0, strpos( \$data->events['11']->TIMESTAMP, ' ' ) )"
		
		)
	)
));