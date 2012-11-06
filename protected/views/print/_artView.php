<div class="row">
	<span class="title bold"><?php echo CHtml::encode($model->DESCRIPTION); ?></span>
	&nbsp;<?php echo ($artLink ? CHtml::link('Download Here', $artLink) : 'No File Found');?>
</div>