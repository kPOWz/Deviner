<div id="customer_content">
	<div class="row lightgrey-bottom">
	<div class="grid_5 alpha">
	<span class="title bold">NAME</span>
	
		<?php //echo CHtml::activeLabelEx($model, 'FIRST');?>
		<?php echo CHtml::encode($model->FIRST);?>
		<?php //echo CHtml::activeLabelEx($model, 'LAST');?>
		<?php echo CHtml::encode($model->LAST);?>
	</div>
	
	<div class="grid_5 omega">
		<span class="title bold"><?php echo CHtml::activeLabelEx($model, 'PHONE');?></span>
		<?php echo CHtml::encode($model->PHONE);?>
		
	</div>
	<div class="clear"></div>
	</div>
	
	<div class="row lightgrey-bottom">
		<div class="grid_5 alpha">
		<span class="title bold"><?php echo CHtml::activeLabelEx($model, 'EMAIL'); ?></span>
		<?php echo $formatter->formatEmail($model->EMAIL);?>
		
		
		</div>
		<div class="grid_5 omega">
		<span class="title bold"><?php echo CHtml::activeLabelEx($model,'COMPANY'); ?></span>
		<?php echo CHtml::encode($model->COMPANY);?>
	</div>
<div class="clear"></div>
	</div>
</div><!-- customer_content -->