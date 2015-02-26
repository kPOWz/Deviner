<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'jobOperations.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($this->scriptDirectory . 'jobEdit.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('add-job', "function addLine(sender, namePrefix){
	var btn = $(sender);
	btn.button('loading');
	var count = $(sender).parents('.row').prev('#lines').children('.jobLines').children('div[name=\"sizes\"]').children('.jobLine').children('.part').size();" .
	"$.ajax({
		url: '".CHtml::normalizeUrl(array('job/newLine'))."'," .
		"type: 'POST'," .
		"data: {
			namePrefix: namePrefix," .
			"count: count,
		}," .
		"success: function(data){
			var productsContainer = $(sender).parents('.gus-form').children('#lines');
			$(data).appendTo(productsContainer);" .
			"var div_id = \$(data).attr('id');" .
			"\$('#' + div_id).find('.item-select').autocomplete({
				'select': function(event, ui){
					\$.getJSON(
					'".CHtml::normalizeUrl(array('product/allowedOptions'))."'," .
					"{
						itemID: ui.item.id," .
						"namePrefix: namePrefix," .
						"count: count,
					}," .
					"function(data){
						var colors = data.colors;" .
						"var sizes = data.sizes;" .
						"var cost = data.productCost;" .
						"var colorOptions = $('<select></select>')" .
							"\n.attr('name', 'color-select')" .
							".attr('class', 'color-select form-control');" .
						"for(var color in colors){
							colorOptions.append($('<option></option>').val(colors[color].ID).html(colors[color].TEXT));
						}" .
						"colorOptions.attr('name', \$('#' + div_id).children('.color-select').attr('name'));" .
						"\$('#' + div_id + ' .row div[name=\"color-group\"]').children('.color-select').replaceWith(colorOptions);\n" .
						"\$('#' + div_id + ' .row').children('.jobLine').addClass('hidden-size').children('.score_part').attr('disabled', true).val(0);" .
						"\$('#' + div_id + ' .row').children('.jobLine').children('.hidden_cost').val(cost);" .
						"onGarmentCostUpdate($('#' + div_id).find('.product-cost'), cost, $('#' + div_id).find('.editable-price'), $('#' + div_id).find('.hidden-price'), $('#' + div_id).find('.garment_part'));" .
						"for(var size in sizes){
							\$('#' + div_id + ' .row').children('.' + div_id + sizes[size].ID)" .
							".removeClass('hidden-size')" .
							".addClass('col-md-2')" .
							".children('.score_part').removeAttr('disabled');
						}" .
						"\$('#' + div_id +' .row div[name=\"style-group\"]').find('.hidden-style').val(ui.item.id);
					});
				}," .
				"'source': '".CHtml::normalizeUrl(array('product/findProduct', 'response'=>'juijson'))."'
			});
		},
	}).always(function(){ btn.button('reset'); });
}", CClientScript::POS_BEGIN);

?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'job-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
		'class'=>'gus-form',
	),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<h4 class="heading-primary">Client Details</h4>
	<?php
		$this->renderPartial('//customer/_jobForm', array(
			'customerList'=>$customerList,
			'newCustomer'=>$newCustomer,
		));
	?>
	<hr />

	<h4 class="heading-primary">Job Details</h4>
	<fieldset class="row">
		<?php $leaderList = CHtml::listData($leaders, 'ID', 'FIRST');?>
		<div class="col-md-4 form-group">
			<?php echo $form->error($model, 'NAME');?>
			<?php echo $form->textField($model, 'NAME', array('class'=>'form-control', 'placeholder'=>'Unique job name for client'));?>			
			<?php echo $form->labelEx($model, 'NAME');?>
		</div>
		<div class="col-md-4 form-group">
			<?php echo $form->error($model, 'LEADER_ID');?>
			<?php echo $form->dropDownList($model, 'LEADER_ID', $leaderList, array('class'=>'form-control', 'prompt'=>'-- Select leader --')); ?>
			<?php echo $form->labelEx($model, 'LEADER_ID');?>
		</div>
		<div class="col-md-4 form-group">
			<?php echo $form->error($model, 'formattedDueDate'); ?>
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'name'=>'Job[formattedDueDate]',
				'model'=>$model,
				'attribute'=>'formattedDueDate',
				'value' => $model->formattedDueDate,
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'DD, MM d, yy',
					'defaultDate'=> $model->formattedDueDate
				),
				'htmlOptions'=>array(
						'class'=>'form-control gus-datepicker'
				),
			));?>
			<?php echo $form->labelEx($model, 'formattedDueDate');?>			
		</div>		
	</fieldset>

	<hr />

	<h4 class="heading-primary">Product Details</h4>
	<fieldset></fieldset>
		<div id="lines">
			<?php
			$index = 0;
			foreach($lineData as $lines){
				$this->renderPartial('//jobLine/_multiForm', array(
					'namePrefix'=>CHtml::activeName($model, 'jobLines'),
					'startIndex'=>$index,
					'products'=>$lines,
					'estimate'=>CostCalculator::calculateTotal($lines['model']->garmentCount, $print->FRONT_PASS, $print->BACK_PASS, $print->SLEEVE_PASS, 0),
					'formatter'=>new Formatter,
				));
				$index += count($lines);
			}?>
		</div><!-- end add product/garment style-->

		<div class="row">
			<div class='col-md-6'>
				<?php echo TbHtml::button('Additional product', array(
					'onclick'=>"addLine(this, '".CHtml::activeName($model, 'jobLines')."');",
					'icon'=>'plus',
					'iconOptions'=>array('class'=>'text-primary'),
					'color'=>'inverse gus-btn',
					'class'=>'form-control',
					'data-loading-text'=>"Adding...",
					
				));?>
			</div>
			<div class='col-md-3 form-group form-group-calculated'>
				<?php $garmentCount = $model->garmentCount;?>
				<?php echo CHtml::textField('garment_qty', $garmentCount, array(
					'id'=>'garment_qty',
					'readonly'=>'readonly',
					'class'=>'form-control',
					'onchange'=>new CJavaScriptExpression(
						"$('#".CHtml::activeId($model, 'QUOTE')."').val($(this).val() * $('#item_total').val());" ),
					'onkeyup'=>new CJavaScriptExpression(
						"$('#".CHtml::activeId($model, 'QUOTE')."').val($(this).val() * $('#item_total').val());" ),
				));?>
				<?php echo CHtml::label('Product Count', 'garment_qty');?>
			</div>
			<div class='col-md-3'>
				<?php $this->renderPartial('//print/_jobForm', array(
					'model'=> $print,
					'job'=>$model,
					'fileTypes'=>$fileTypes,
					'passes'=>$passes,
				));?>
			</div>
		</div>
	</fieldset>

	<hr />


	<h4 class="heading-primary">Pricing Details</h4>
	<fieldset class="auto_quote">

		<div class="row">
			<!-- Rush Fee Group-->
			<div class="col-md-2 form-group">
				<?php echo $form->error($model,'RUSH'); ?>
				<div class="input-group gus-input-group">
					<span class="input-group-addon">$</span>		
					<?php echo $form->textField($model,'RUSH', array('class'=>'part form-control')); ?>
				</div>
				<?php echo $form->labelEx($model,'RUSH'); ?>
			</div>

			<!-- Art Fee Group-->
			<div class="col-md-2 form-group">
				<?php echo CHtml::error($print,'COST'); ?>
				<div class="input-group gus-input-group">
					<span class="input-group-addon">$</span>
					<?php echo CHtml::activeTextField($print,'COST',array('size'=>6,'maxlength'=>6, 'class'=>'part form-control')); ?>
				</div>
				<?php echo CHtml::activeLabelEx($print,'COST'); ?>
			</div>

			<!-- Shipping Fee Group-->
			<div class="col-md-2 form-group">
				<?php echo CHtml::error($model,'additionalFees['.Job::FEE_SHIPPING.']'); ?>
				<div class="input-group gus-input-group">
					<span class="input-group-addon">$</span>			
					<?php echo $form->textField($model, 'additionalFees['.Job::FEE_SHIPPING.']', array(
						'value'=>$model->additionalFees[Job::FEE_SHIPPING]['VALUE'],
						'size'=>6,
						'maxlength'=>6,
						'class'=>($model->additionalFees[Job::FEE_SHIPPING]['CONSTRAINTS']['part'] !== false) ? 'part form-control' : 'form-control',
					));?>
				</div>
				<?php echo $form->labelEx($model, 'additionalFees['.Job::FEE_SHIPPING.']', array(
					'label'=>$model->additionalFees[Job::FEE_SHIPPING]['TEXT'],));?>
			</div>

			<!-- Ink Change Fee Group-->
			<div class="col-md-2 form-group">
				<div class="input-group gus-input-group">
					<span class="input-group-addon">$</span>				
					<input placeholder="not implemented" class="form-control" />
				</div>
				<label>Ink Change Fee</label>
			</div>

			<!-- Setup Fee Group-->
			<div class="col-md-2 form-group">
			    <?php echo CHtml::error($model,'SET_UP_FEE'); ?>		    	
		    	<div class="input-group gus-input-group">
			      <span class="input-group-addon">
			        <?php echo CHtml::activeCheckBox($model,'SET_UP_FEE', array(
			    		'value'=>GlobalConstants::SETUP_FEE_AMOUNT_DEFAULT,
			    		'uncheckValue'=> GlobalConstants::SETUP_FEE_AMOUNT_WAIVED,
			    		'class'=>'part editable-fee',
			    		'onchange'=>"$('#setup-fee-hint').val('$' + ($(this).is(':checked') ? $(this).val() : '0') + '.00')"
		    		)); ?>
			      </span>
			      <input type="text" readonly class="form-control intToUsd" id='setup-fee-hint' 
			      	value="<?php echo Yii::app()->numberFormatter->formatCurrency(GlobalConstants::SETUP_FEE_AMOUNT_WAIVED, '$') ?>" />
			    </div>
			    <?php echo CHtml::activeLabelEx($model,'SET_UP_FEE'); ?>		    	
			</div>
			<div class="col-md-2 form-group form-group-calculated">
				<div class="input-group gus-input-group">							
					<input class="form-control" id="jobCogPercentage" readonly placeholder="N/A" 
					value=
						<?php echo CHtml::encode(Yii::app()->numberFormatter->format('#,##0', ($model->total >0 ? $model->costOfGoodsSold / $model->total : 0) * 100)); ?> />
					<span class="input-group-addon">%</span>
				</div>
				<label>Cost of Goods</label>
			</div>
		</div>	

		<!-- Total Group-->
		<div class='row'>
			<div class="col-md-12 form-group">
				<div class="input-group gus-input-group form-group-calculated">
					<span class="input-group-addon">$</span>		
					<input id="jobTotal" class="form-control" readonly placeholder="not implemented" value=
						<?php echo CHtml::encode(Yii::app()->numberFormatter->formatDecimal($model->total)); ?> />

					<?php $taxRate = $model->additionalFees[Job::FEE_TAX_RATE]['VALUE'];
						 echo CHtml::hiddenField('tax_rate', $taxRate); ?>
				</div>
				<label class="form-group-calculated gus-btn">Total</label>
				<label class="text-muted">
					<?php echo CHtml::activeCheckBox($model,'additionalFees['.Job::FEE_TAX_RATE.']'
						, array('checked'=>'checked', 'id'=>'jobIsTaxed'));?> 
					<?php echo $model->additionalFees[Job::FEE_TAX_RATE]['VALUE'];?>% Sales Tax
				</label>
			</div>
		</div>


		<!-- TODO: WORKING? -->
		<?php Yii::app()->clientScript->registerScript('auto-garment-totaler', "" .
				"$('.item_qty').live('change keyup', function(){
					var qty = 0;" .
					"$('.item_qty').each(function(index){
						qty += (1 * $(this).val());
					});" .
					"$('#garment_qty').val(qty).change();" .
					"updateSetupCost('".CHtml::normalizeUrl(array('job/setupFee'))."', $('.editable-fee'), $('#setup-fee-hint'), qty);".
					"calculateJobTotal();".
				"})",
		CClientScript::POS_END);?>
	</fieldset> <!-- <div class="row auto_quote">-->

	<!-- Job Score Group-->
	<!-- TODO: REMOVE -->
	<div class="row hidden">
		<?php echo CHtml::hiddenField('score_base', 30, array('class'=>'score_base'));?>
		<?php /*echo $form->labelEx($model, 'SCORE');?>
		<?php echo CHtml::textField('score', $model->score, array(
			'id'=>'score',
			'readonly'=>'readonly',
		));*/?>
		<?php Yii::app()->clientScript->registerScript('auto-score', "" .
				"$('.score_part, .score_pass').live('change keyup', function(){
					var base = 1 * $('.score_base').val();" .
					"var passes = 1 * $('.score_pass').val();" .
					"var qty = 0;" .
					"$('.score_part').each(function(index){
						qty += 1 * $(this).val();
					});" .
					"$('#score').val(base + (passes * qty));
				});",
		CClientScript::POS_END);?>
	</div>

	<hr />

	<div class="row">
		<div class="col-md-6">
			<?php echo $form->error($model,'NOTES'); ?>
			<?php echo $form->textArea($model,'NOTES',array('rows'=>6, 'cols'=>50, 'class'=>'form-control')); ?>			
			<?php echo $form->labelEx($model,'NOTES'); ?>
		</div>
		<div class="col-md-6">
			<div class="input-group">  
			    <label id="jobStatusDisplay" class="form-control gus-btn" readonly>Job Status: 
			    	<span class="selection text-primary h5 heading-primary"><?php echo $model->isNewRecord ? 'CREATED' : $model->status->TEXT ?></span>
			    </label>
			    <?php echo TbHtml::activeHiddenField($model, 'STATUS') ?>
			    <div class="input-group-btn">
			    	<?php echo TbHtml::buttonDropdown('', Job::statusButtonData()
			    		, array(
			    				'type'=> TbHtml::BUTTON_TYPE_HTML
			    				,'data-target'=>'#'
			    				,'href'=>''
			    				,'class'=>'text-primary'
			    				,'groupOptions'=>array('class'=>'dropdown', 'id'=>'jobStatusDropdown')
			    				,'menuOptions'=>array('class'=>'dropdown-menu-right'))); 
			    	?>
			        <?php echo CHtml::htmlButton('<span class="glyphicon glyphicon-ok text-success"/>', array(
						'class'=> 'btn btn-default',
						'type'=>'submit',
						'title'=>'save'
					)); ?>
			    </div>
		 	</div>
		</div>
	</div>
	
	<hr />
<?php $this->endWidget(); ?>

