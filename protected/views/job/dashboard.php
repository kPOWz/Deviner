<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'Dashboard | GUS';
?>

<h1>My Jobs</h1>				
<!--table goes here-->
<?php 
$this->renderPartial('_list', array(
		'statuses'=>$statuses,
		'dataProvider'=>$dataProvider,
		'tabId'=>'job-tab-current'));
?>