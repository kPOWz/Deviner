<?php $id = 'job_evt_'.$job->ID;?>

<div id="<?php echo $id;?>">
	<?php if($job->STATUS == Job::INVOICED || $job->STATUS == Job::PRINTED){?>
			<span class="<?php echo $job->LEADER_ID == Yii::app()->user->id || $job->PRINTER_ID == Yii::app()->user->id ?
								 '' : 'text-primary'?>" >
				&#10003;</span> 
	<?php } else {?>
		<span class="text-primary">&#9679;</span>
	<?php }?>
	<a href="<?php echo CHtml::normalizeUrl(array('job/update', 'id'=>$job->ID));?>">
		<?php echo CHtml::encode($job->NAME);?>
	</a>
	<?php echo CHtml::activeHiddenField($job, 'ID');?>
</div>
