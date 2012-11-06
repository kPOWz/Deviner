	<h1>Cost Estimator</h1>

<?php echo $this->renderPartial('_estimate', array(
	'model'=>$model,
	'print'=>$print,
	'passes'=>$passes,
	'lineData'=>$lineData,
)); ?>

<p>*This is an estimate; the final price may differ based on a multitude of things. That being said, we aren't dicks and will try to stick as close to this as possible.</p>