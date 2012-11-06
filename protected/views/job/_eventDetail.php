<?php $job = $item->assocObject;?>
<?php $id = 'job_evt_'.$job->ID.$item->ID;?>
<div id="<?php echo $id;?>">
	<div class="pad"><?php 
		$job = $item->getAssocObject();
	?>
	<?php if($job->RUSH){?>
		<span class="warning">RUSH</span>&nbsp;
	<?php } ?>
	<a href="/index.php?r=job/view&id=<?php echo CHtml::encode($job->ID);?>"><?php echo CHtml::encode($job->NAME);?></a>&nbsp;(<strong><?php echo $job->score;?></strong>)
	<?php echo CHtml::activeHiddenField($item, 'ID');?>
	<?php Yii::app()->clientScript->registerCss($id, "#$id{}");/*480 is number of minutes in 8 hours*/?></div>
</div>