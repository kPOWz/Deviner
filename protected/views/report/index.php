<?php
$this->breadcrumbs=array(
	'Reports',
);
?>

<h1>Reports</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'class'=>'CLinkColumn',
			'labelExpression'=>"\$data->FIRST . ' ' . \$data->LAST",
			'urlExpression'=>"array('user/update', 'id'=>\$data->ID)",
			'header'=>'Name',
		),
		'EMAIL:email:Email',
		array(
			'header'=>'Primary Role',
			'value'=>"\$data->isAdmin ? 'Administrator' : \$data->isCustomer ? 'Customer' : \$data->isLead ? 'Project Lead' : 'Printer'",
		),
	),
));?>
