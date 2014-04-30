<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'My Jobs | GUS';
?>

<h1>My Jobs</h1>
<?php if(Yii::app()->user->isLead){?>
	<h3>Month's Sales : <?php echo CHtml::encode($formatter->formatCurrency($monthSales)); ?></h3>
<?php }?>

<?php 
$this->renderPartial('_list', array(
		'statuses'=>$statuses,
		'dataProvider'=>$dataProvider,
		'tabId'=>'job-tab-current'));
?>