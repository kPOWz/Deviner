<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'My Jobs | GUS';
?>

<h1>My Jobs</h1>
<?php if(Yii::app()->user->isLead){?>
	<h3 title="Dollar value of completed jobs for current calendar month">
		Month's Sales : &nbsp;<?php echo CHtml::encode($formatter->formatCurrency($monthSales)); ?>
	</h3>
	<h3 title="Percentage of current calendar month's sales allotted to cost of goods sold">
		Month's COGS % : &nbsp;<?php echo $monthSales > 0 ? 
		CHtml::encode($formatter->formatPercentage($monthCostOfGoodsSoldPercentage)) : "N/A" ?>
	</h3>
<?php }?>

<?php 
$this->renderPartial('_list', array(
		'statuses'=>$statuses,
		'dataProvider'=>$dataProvider,
		'tabId'=>'job-tab-current'));
?>