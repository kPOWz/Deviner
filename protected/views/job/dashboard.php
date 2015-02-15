<?php
	$this->pageTitle = Yii::app()->user->name . ' | ' . 'My Jobs | GUS';
?>

<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	<span>Welcome, <?php echo Yii::app()->user->name;?>!</span>&nbsp;
	You have X jobs.
	<?php if(Yii::app()->user->isLead){?>
		Here are your monthly sales: $X,XXX. Your Cost of Goods percentage for this month is X%.
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