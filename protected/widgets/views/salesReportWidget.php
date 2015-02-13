<?php if(Yii::app()->user->isLead){?>
	<div class="col-md-6 " title="Dollar value of completed jobs for current calendar month">
		<span class="text-primary" name='salesNumber'><?php echo CHtml::encode($formatter->formatCurrency($sales)); ?></span>
		<br />
		<small>Monthly Sales</small>
	</div>
	<div class="col-md-6" title="Percentage of current calendar month's sales allotted to cost of goods sold">
		<span class="text-primary" name='salesPercentage'><?php echo $sales > 0 ? CHtml::encode($formatter->formatPercentage($cog)) : "N/A" ?></span>
		<br />
		<small>Monthly COG</small>
	</div>
<?php }?>