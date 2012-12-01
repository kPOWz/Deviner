<?php
$this->pageTitle=Yii::app()->name . ' - Dashboard';
$this->breadcrumbs=array(
		'Dashboard',
);
?>
<h1>Dashboard</h1>

<?php echo $this->renderPartial('//job/_export')?>