<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'My Jobs | GUS';
?>

<h1>My Jobs</h1>

<?php
	if(Yii::app()->user->getState('isLead'))
		$this->widget('application.widgets.SalesReportWidget'); 
?>

<?php 
	$this->renderPartial('_list', array(
		'statuses'=>$statuses,
		'dataProvider'=>$dataProvider,
		'tabId'=>'job-tab-current'));
?>