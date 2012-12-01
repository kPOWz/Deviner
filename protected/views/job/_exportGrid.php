<?php $this->widget('zii.widgets.grid.CGridView', array( 
	'dataProvider'=>$exportData,
	'formatter'=>new Formatter,
	'columns'=>array(
		array(
			'header'=>'ID',
			'value' => "\$data->ID"
		),
		array(
			'header' => 'NAME',
			'value' => "\$data->NAME"
				
		),
		array(
				'header' => 'STATUS',
				'value' => "\$data->STATUS"
		
		)
	)
));