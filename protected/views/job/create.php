<?php
$this->breadcrumbs=array(
	'Jobs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Job', 'url'=>array('index')),
	array('label'=>'Manage Job', 'url'=>array('admin')),
);
?>

<h2>Create Job</h2>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'customerList'=>$customerList,
	'newCustomer'=>$newCustomer,
	'leaders'=>$leaders,
	'printers'=>$printers,
	'colors'=>$colors,
	'sizes'=>$sizes,
	'print'=>$print,
	'passes'=>$passes,
	'lineData'=>$lineData,
	'fileTypes'=>$fileTypes,
	'formatter'=>new Formatter
)); ?>