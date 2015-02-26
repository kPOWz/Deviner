<?php $id = 'job_evt_'.$job->ID;?>

<div id="<?php echo $id;?>">
	<a href="<?php echo CHtml::normalizeUrl(array('job/update', 'id'=>$job->ID));?>">
		<span class="text-primary">&#9679</span> 
		<?php echo CHtml::encode($job->NAME);?>
	</a>
	<?php echo CHtml::activeHiddenField($job, 'ID');?>
</div>
