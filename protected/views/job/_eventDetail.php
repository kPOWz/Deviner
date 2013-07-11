<?php $id = 'job_evt_'.$job->ID;?>

<div id="<?php echo $id;?>">
	<a href="<?php echo CHtml::normalizeUrl(array('job/view', 'id'=>$job->ID));?>">
		<?php echo CHtml::encode($job->NAME);?>
	</a>
	<?php echo CHtml::activeHiddenField($job, 'ID');?>
</div>
