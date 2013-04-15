<div id="print_content">


	<div class="row passes">
		<span class="title bold">INK COLOR</span><?php //echo CHtml::activeLabelEx($model,'FRONT_PASS'); ?>
		<?php echo CHtml::encode($model->FRONT_PASS);?> <strong>/</strong>
		<?php //echo CHtml::activeLabelEx($model,'BACK_PASS'); ?>
		<?php echo CHtml::encode($model->BACK_PASS);?> <strong>/</strong> 
		<?php //echo CHtml::activeLabelEx($model,'SLEEVE_PASS'); ?>
		<?php echo CHtml::encode($model->SLEEVE_PASS);?>
	</div>
	
	<div class="row art">
		<?php 
		foreach($model->files as $art){?>
			<?php $this->renderPartial('//print/_artView', array(
				'model'=>$art,
				'artLink'=>isset($art->FILE) && is_string($art->FILE) ? CHtml::normalizeUrl(array('job/art', 'art_id'=>$art->ID, 'id'=>$jobId)) : null,
			));?>
		<?php }?>
	</div>
</div><!-- print_content -->