<?php if(Yii::app()->user->isLead && !$raw){?>
	<div class="col-md-6 " title="Dollar value of completed jobs for current calendar month">
		<strong class="text-primary" name='salesNumber'><?php echo CHtml::encode($formatter->formatCurrency($sales)); ?></strong>
		<br />
		<small>Monthly Sales</small>
	</div>
	<div class="col-md-6" title="Percentage of current calendar month's sales allotted to cost of goods sold">
		<strong class="text-primary" name='salesPercentage'><?php echo $sales > 0 ? CHtml::encode($formatter->formatPercentage($cog)) : "N/A" ?></strong>
		<br />
		<small>Monthly COG</small>
	</div>
<?php }?>