<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'My Jobs | GUS';
	$srw = $this->widget('application.widgets.SalesReportWidget', array('raw'=>true));
?>

<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	<span>Welcome, <?php echo Yii::app()->user->name;?>!</span>&nbsp;
	You have <span class="text-inverse"> <?php echo count($dataProvider) ?> job(s)</span>.
	<?php if(Yii::app()->user->isLead){?>
		Here are your monthly sales: <span class="text-inverse" name='salesNumber'>
			<?php echo CHtml::encode(Yii::app()->numberFormatter->formatCurrency($srw->sales, "$")); ?>
		</span>. 
		Your Cost of Goods percentage for this month is <span class="text-inverse" name='salesPercentage'>
			<?php echo $srw->sales > 0 ? CHtml::encode(Yii::app()->numberFormatter->formatPercentage($srw->costOfGoodsSoldPercentage)) : "N/A" ?>
		</span>.
	<?php }?>
	Keep doin' whatcha do!
	<!--<div class="messages">
		<?php foreach($this->messages as $message){?>
			<strong><?php echo $message;?></strong>
			<br/>
		<?php }?>
	</div>-->
</div>

<h1>My Jobs</h1>

<?php 
	$this->renderPartial('_list', array(
		'statuses'=>$statuses,
		'dataProvider'=>$dataProvider,
		'tabId'=>'job-tab-current'));
?>