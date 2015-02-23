<fieldset id="customer">
	<?php echo CHtml::errorSummary($newCustomer); ?>
	
	<div class="row">
		<div class="col-md-7 form-group search-customer">
			<div class="input-group gus-input-group">
				<?php $customerSelections = CHtml::listData($customerList, 'ID', 'summary');?>
				<?php $ac = $this->beginWidget('zii.widgets.jui.CJuiAutoComplete', array(
					'model'=>$newCustomer,
					'attribute'=>'summary',
					'sourceUrl'=>array('customer/search', 'response'=>'juijson'),
					'options'=>array(),
					'htmlOptions'=>array(
							'class'=>'form-control',
							'placeholder'=>'Search existing customers'
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
				<span class="input-group-addon">
				   	<span class="glyphicon glyphicon-search text-primary"></span>
		   		</span>
			</div>
			<?php echo CHtml::activeHiddenField($newCustomer, 'ID')?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3 form-group">
			<?php echo CHtml::error($newCustomer,'COMPANY'); ?>
			<?php echo CHtml::activeTextField($newCustomer,'COMPANY',array('size'=>58,'maxlength'=>58, 'class'=>'form-control')); ?>			
			<?php echo CHtml::activeLabelEx($newCustomer,'COMPANY'); ?>
		</div>
		<div class="col-md-2 form-group">
			<?php echo CHtml::error($newCustomer, 'FIRST');?>
			<?php echo CHtml::activeTextField($newCustomer, 'FIRST',array('size'=>32, 'class'=>'form-control'));?>		
			<?php echo CHtml::activeLabelEx($newCustomer, 'FIRST');?>
		</div>
		
		<div class="col-md-2 form-group">
			<?php echo CHtml::error($newCustomer, 'LAST');?>			
			<?php echo CHtml::activeTextField($newCustomer, 'LAST',array('size'=>32, 'class'=>'form-control'));?>
			<?php echo CHtml::activeLabelEx($newCustomer, 'LAST');?>
		</div>
		
		<div class="col-md-3 form-group">
			<?php echo CHtml::error($newCustomer, 'EMAIL'); ?>
			<?php echo CHtml::activeTextField($newCustomer, 'EMAIL', array('class'=>'form-control')); ?>			
			<?php echo CHtml::activeLabelEx($newCustomer, 'EMAIL'); ?>
		</div>
		<div class="col-md-2 form-group">
			<?php echo CHtml::error($newCustomer, 'PHONE');?>
			<?php echo CHtml::activeTextField($newCustomer, 'PHONE', array('class'=>'form-control'));?>			
			<?php echo CHtml::activeLabelEx($newCustomer, 'PHONE');?>
		</div>
	</div>

</fieldset>