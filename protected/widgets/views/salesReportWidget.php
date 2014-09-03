<?php if(Yii::app()->user->isLead){?>
	<div title="Dollar value of completed jobs for current calendar month">
		<span name='salesNumber'><?php echo CHtml::encode($formatter->formatCurrency($sales)); ?></span>
		Monthly Sales
	</div>
	<div title="Percentage of current calendar month's sales allotted to cost of goods sold">
		<span name='salesPercentage'><?php echo $sales > 0 ? CHtml::encode($formatter->formatPercentage($cog)) : "N/A" ?></span>
		Monthly COG
	</div>
<?php }?>