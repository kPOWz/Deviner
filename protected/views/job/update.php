<h1>Update Job <small><?php echo $model->NAME; ?></small></h1>

<?php echo $this->renderPartial('_form', array(
	'id'=>'job-form-update',
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