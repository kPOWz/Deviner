function calculateTotalCore(url, garments, front, back, sleeve, completion){
	$.getJSON(url,
	{
		garments: garments,
		front: front,
		back: back,
		sleeve: sleeve,
	},
	completion
	);
}

function calculateSetupFeeCore(url, garments, completion){
	$.getJSON(url,
	{
		garments: garments
	},
	completion
	);
}

function calculateTotalMain(url, garments, front, back, sleeve, dest){
	calculateTotalCore(url, garments, front, back, sleeve, function(data){
		$(dest).val(data.result).change();
	});
}

function calculateSetupFeeMain(url, garments, dest){
	calculateSetupFeeCore(url, garments, function(data){
		$(dest).val(data.result).change();
	});
}

function getFrontPasses(){
	return $('.front_pass').val();
}

function getBackPasses(){
	return $('.back_pass').val();
}

function getSleevePasses(){
	return $('.sleeve_pass').val();
}

function getGarmentCount(estimate){
	var garmentCount = 0;
	$(estimate).parentsUntil('.jobLines').parent().find('.item_qty').each(function(){
		garmentCount += 1 * $(this).val();
	});
	return garmentCount;
}

function getSizeSurChargeSum(sender) {
	var sizeSurChargeSum =0;
	var sizeSurChargeFields = $(sender).closest('.jobLines').children('*[name="sizes"]').children('.jobLine[class*="col-"]').find('.part');
	$.each(sizeSurChargeFields, function(idx, jobLineSizeFee){
									sizeSurChargeSum +=parseFloat($(jobLineSizeFee).val()); });
	return sizeSurChargeSum;
}

function refreshEstimate(editVal, estimateVal, estimate){	
	$(estimate).children('span').html('$'+estimateVal);
	$(estimate).children('.hidden-price').val(estimateVal);
	if(estimateVal == editVal){
		$(estimate).hide();
	} else {
		$(estimate).show();
	}
}

function recalculateJobLineTotal(editable, estimate, total){
	var editVal = $(editable).val() * 1;
	var garmentCount = getGarmentCount(estimate);
	refreshEstimate(editVal, $(estimate).children('.hidden-price').val(), estimate);

	var sizeSurChargeSum = getSizeSurChargeSum(editable);
	$(total).val((editVal * garmentCount)+ sizeSurChargeSum).change();
}

function createStyleSelectFunction(div_id, style_id){
	return function(data){
		var colors = data.colors;
		var sizes = data.sizes;
		var cost = data.productCost;
		var colorOptions = $('<select></select>')
			.attr('name', 'color-select')
			.attr('class', 'color-select');
		for(var color in colors){
			colorOptions.append($('<option></option>').val(colors[color].ID).html(colors[color].TEXT));
		}
		colorOptions.attr('name', $('#' + div_id).children('.color-select').attr('name'));
		$('#' + div_id).children('.color-select').replaceWith(colorOptions);
		$('#' + div_id).children('.jobLine').addClass('hidden-size').children('.score_part').attr('disabled', true).val(0);
		$('#' + div_id).children('.jobLine').children('.hidden_cost').val(cost);
		onGarmentCostUpdate($('#' + div_id).find('.product-cost'), cost, $('#' + div_id).find('.editable-price'), $('#' + div_id).find('.hidden-price'), $('#' + div_id).find('.garment_part'));
		for(var size in sizes){
			$('#' + div_id).children('.' + div_id + sizes[size].ID)
			.removeClass('hidden-size')
			.parent().children('.score_part').removeAttr('disabled');
		}
		$('#' + div_id).children('.hidden-style').val(style_id);
	}
}

function updateSetupCost(url, editable, garmentCount){
	var editVal = $(editable).val() * 1;
	calculateSetupFeeCore(url, garmentCount, function(data){
		var newCost = data.result;
		editable.removeAttr('value');
		editable.prop('placeholder', newCost.toFixed(2));
	});
}

//returns a function which totals up all of the parts of a job
function autoTotal(taxRateField){
	return function(){
		var total = 0;
		var tax = (1 * $(taxRateField).val()) / 100;
		var totalEach = 0;
		$('.part').each(function(index){
			if($(this).is(':checkbox') && !($(this).is(":checked"))) return; //skip current iteration so as not to add values of unchecked booleans
			total += (1 * $(this).val());
		});
		$('#auto_total').val(parseFloat(total).toFixed(2));
		$('#auto_tax').val(parseFloat(total * tax).toFixed(2));
		$('#auto_grand').val(parseFloat(total * (1 + tax)).toFixed(2));
		
		var qty = 0;
		$('.item_qty').each(function(index){
			qty += (1 * $(this).val());
		});
		totalEach = (qty == 0) ? 0 : total / qty;
		$('#auto_total_each').val(parseFloat(totalEach).toFixed(2));
		$('#auto_tax_each').val(parseFloat(totalEach * tax).toFixed(2));
		$('#auto_grand_each').val(parseFloat(totalEach * (1 + tax)).toFixed(2));
		if(qty > 200){
			$('#auto_total, #auto_total_each, #auto_tax, #auto_tax_each, #auto_grand, #auto_grand_each').val(0).attr('disabled', 'disabled');
			$('#qty_warning').show();
		} else {
			$('#auto_total, #auto_total_each, #auto_tax, #auto_tax_each, #auto_grand, #auto_grand_each').removeAttr('disabled');
			$('#qty_warning').hide();
		}
	};
}