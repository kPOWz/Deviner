<div id="customer" class="form">

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo CHtml::errorSummary($newCustomer); ?>
	
	<div class="row search-customer">
		<?php $customerSelections = CHtml::listData($customerList, 'ID', 'summary');?>
		<?php echo 'Search Existing Customers: ';?>
		<?php $ac = $this->beginWidget('zii.widgets.jui.CJuiAutoComplete', array(
			'model'=>$newCustomer,
			'attribute'=>'summary',
			'sourceUrl'=>array('customer/search', 'response'=>'juijson'),
			'options'=>array(),
			'htmlOptions'=>array(
					'class'=>'input_xlarge'
			)
		));
		
		$ac->options['select'] = new CJavaScriptExpression(
			"function(event, ui){" .
				"var value = ui.item.value;" .
				"var label = ui.item.label;" .
				"var id = '#".CHtml::activeId($newCustomer, 'ID')."';" .
				"$(id).val(value);" .
				"$('#".$ac->id."').val(label);" .
				"$.ajax({
					url: '".CHtml::normalizeUrl(array('customer/retrieve'))."'," .
					"type: 'POST'," .
					"data: {
						id: $(id).val(),
					}," .
					"success: function(data){
						$('#".CHtml::activeId($newCustomer, 'FIRST')."').val(data.FIRST);" .
						"$('#".CHtml::activeId($newCustomer, 'LAST')."').val(data.LAST);" .
						"$('#".CHtml::activeId($newCustomer, 'EMAIL')."').val(data.EMAIL);" .
						"$('#".CHtml::activeId($newCustomer, 'COMPANY')."').val(data.COMPANY);" .
						"$('#".CHtml::activeId($newCustomer, 'PHONE')."').val(data.PHONE);
					}," .
					"error: function(){
						$(id).val('');
					}," .
					"dataType: 'json',
				});" .
				"event.stopImmediatePropagation();" .
				"return false;" .
			"}");
								
		$this->endWidget();?>
		<?php echo CHtml::activeHiddenField($newCustomer, 'ID')?>
	</div>
	
	<div class="row">
	<div class="grid_3 alpha">
		<?php echo CHtml::activeLabelEx($newCustomer, 'FIRST');?>
		<?php echo CHtml::activeTextField($newCustomer, 'FIRST',array('size'=>32));?>
		<?php echo CHtml::error($newCustomer, 'FIRST');?>
	</div>
	
	<div class="grid_3">
		<?php echo CHtml::activeLabelEx($newCustomer, 'LAST');?>
		<?php echo CHtml::activeTextField($newCustomer, 'LAST',array('size'=>32));?>
		<?php echo CHtml::error($newCustomer, 'LAST');?>
	</div>
	
	<div class="grid_4 omega">
		<?php echo CHtml::activeLabelEx($newCustomer, 'EMAIL'); ?>
		<?php echo CHtml::activeTextField($newCustomer, 'EMAIL'); ?>
		<?php echo CHtml::error($newCustomer, 'EMAIL'); ?>
	</div>
	<div class="clear"></div>
	</div>
	
	<div class="row">
	<div class="grid_5 alpha">
		<?php echo CHtml::activeLabelEx($newCustomer,'COMPANY'); ?>
		<?php echo CHtml::activeTextField($newCustomer,'COMPANY',array('size'=>58,'maxlength'=>58)); ?>
		<?php echo CHtml::error($newCustomer,'COMPANY'); ?>
	</div>
	
	
	<div class="grid_5 omega">
		<?php echo CHtml::activeLabelEx($newCustomer, 'PHONE');?>
		<?php echo CHtml::activeTextField($newCustomer, 'PHONE');?>
		<?php echo CHtml::error($newCustomer, 'PHONE');?>
	</div>
	<div class="clear"></div>
	</div>

</div><!-- form -->