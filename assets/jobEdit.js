function onGarmentCostUpdate(costField, newCost, editable, estimate, total){
	var oldCost = $(costField).val() * 1;
	var garmentCount = getGarmentCount(estimate);
	var oldEstimate = $(estimate).val() * 1;
	var newEstimate = oldEstimate + 1 * newCost - oldCost;
	var editVal = $(editable).val() * 1;
	if(oldCost != newCost){
		if(oldEstimate == editVal){
			editVal = newEstimate;
			$(editable).val(editVal);
		}
		refreshEstimate(editVal, newEstimate, $(estimate).parent());
		$(costField).val(newCost);
		$(total).val(editVal * garmentCount).change();		
	}
}

function updateLineTotal(sender, calculatorUrl, editable, estimate, total, cost){
	var editVal = $(editable).val() * 1;
	var costVal = $(cost).val() * 1;
	var totalVal = 0;
	var garmentCount = getGarmentCount(estimate);
	var estimateVal = 0;
	calculateTotalCore(calculatorUrl, garmentCount, getFrontPasses(), getBackPasses(), getSleevePasses(), function(data){
		var oldEstimate = $(estimate).children('.hidden-price').val() * 1;
		if(oldEstimate == editVal){
			editVal = 1 * data.result / garmentCount + costVal;
			$(editable).val(editVal);
		}
		refreshEstimate(editVal, 1 * data.result / garmentCount + costVal, estimate);
		var sizeSurChargeSum =getSizeSurChargeSum(sender);
		$(total).val((editVal * garmentCount)+ sizeSurChargeSum).change();
	});
	$(sender).closest('*[name="sizes"]').children('*[name="product-quantity"]:first').val(garmentCount);
}

function chooseEstimatePrice(element, event){
	event.preventDefault();
	$(element).parents('.price-select-container')
		.find('.unit_price')
			.val($(element).children('.hidden-price').val())
			.keyup(); 
	$(element).hide();

	return false;
}

var getTaxRate = function(){
	var taxRate = 0;
	if($('#jobIsTaxed').prop('checked')){
		taxRate = parseInt($('#tax_rate').val(), 10);
		taxRate = (typeof taxRate  === "number" && !isNaN(taxRate)) ? taxRate : 0;		
	}
	return taxRate;
}

var calculateSalesTax = function(subTotal){
	return parseFloat((getTaxRate()/100 * subTotal).toFixed(2));
}

var setGrandTotal = function(subTotal){
	var gTotal = subTotal + calculateSalesTax(subTotal);
	$('#jobTotal').val(gTotal.toFixed(2)).change();
}

var setCostOfGoodsSoldPercentage = function(costOfGoods){
	var totalBeforeTax = parseFloat($("#jobTotal" ).val());
	totalBeforeTax = (typeof totalBeforeTax  === "number" && !isNaN(totalBeforeTax)) ? totalBeforeTax : 0;
	//remove tax if necessary
	if($('#jobIsTaxed').prop('checked')){
		totalBeforeTax = totalBeforeTax / (getTaxRate()/100 + 1);
	}
	$('#jobCogPercentage').val(totalBeforeTax > 0 ? 
		Math.round(parseFloat(((costOfGoods / totalBeforeTax).toFixed(2)) * 100)) : '');
}

var calculateJobTotal = function(){
	var jobTotal = 0;
	$('.auto_quote .part').each(function(index){
		var lineItemPrice = parseFloat($(this).val());
		lineItemPrice = (typeof lineItemPrice  === "number" && !isNaN(lineItemPrice)) ? lineItemPrice : 0;
		jobTotal += lineItemPrice; 
	});
	if(!($('#Job_SET_UP_FEE').prop('checked'))) jobTotal -= 30; //remove setup fee
	$('#lines div[name="price-group"] .garment_part').each(function(index){ jobTotal += parseFloat($(this).val()); });
	setGrandTotal(jobTotal);
}

function addJobLine(sender, namePrefix, newJobLineUrl, productOptionsUrl, productFindUrl){
	var btn = $(sender);
	btn.button('loading');
	var count = $(sender).parents('.row').prev('#lines').children('.jobLines')
							.children('div[name="sizes"]').children('.jobLine').children('.part').size();
	$.ajax({
		url: newJobLineUrl,
		type: 'POST',
		data: {
			namePrefix: namePrefix,
			count: count,
		},
		success: function(data){
			var productsContainer = $(sender).parents('.gus-form').children('#lines');
			$(data).appendTo(productsContainer);
			var div_id = $(data).find('.line_delete').parent().attr('id');
			$('#' + div_id).find('.item-select').autocomplete({
				'select': function(event, ui){
					$.getJSON( productOptionsUrl,{
							itemID: ui.item.id,
							namePrefix: namePrefix,
							count: count,
						},
						function(data){
							var colors = data.colors;
							var sizes = data.sizes;
							var cost = data.productCost;
							var selectElement = $('#' + div_id + ' .row div[name="color-group"]').find('.color-select:first-child');
							selectElement.empty();
							for(var color in colors){
								selectElement.append($('<option></option>').val(colors[color].ID).html(colors[color].TEXT));
							}
							selectElement.removeAttr('disabled');
							var jobSizeLine = $('#' + div_id + ' .row').children('.jobLine');
							jobSizeLine.addClass('hidden-size').removeClass('col-md-2')
										.children('.score_part').attr('disabled', true).val(0);
							jobSizeLine.children('.hidden_cost').val(cost);
							onGarmentCostUpdate($('#' + div_id).find('.product-cost')
								, cost, $('#' + div_id).find('.editable-price')
								, $('#' + div_id).find('.hidden-price')
								, $('#' + div_id).find('.garment_part')
							);
							for(var size in sizes){
								$('#' + div_id + ' .row').children('.' + div_id + sizes[size].ID)
								.removeClass('hidden-size')
								.addClass('col-md-2')
								.children('.score_part').removeAttr('disabled');
							}
							$('#' + div_id +' .row div[name="style-group"]').find('.hidden-style').val(ui.item.id);
						}
					);
				},
				'source': productFindUrl
			});
		},
	}).always(function(){ btn.button('reset'); });
}

//need to distinguish betwene this and a normal submit
// $( "#job-form-update" ).on( "submit", function(event) {
//   	event.preventDefault();

// });

var calculateCostOfGoodsPercentage = function(){
	var costOfGoods = 0;
	var jobProducts = $('.jobLines')
	$.each(jobProducts, function(idx, jobProduct){
									costOfGoods += $(jobProduct).find('*[name="product-cost"]').val() 
										* $(jobProduct).find('*[name="product-quantity"]').val(); 
								});
	setCostOfGoodsSoldPercentage(costOfGoods);
}

var addAutoTotalListeners = function(){
	//any fee input change
	$( ".auto_quote .part" ).on( "change keyup", function() {
	  	calculateJobTotal();
	});
	//is taxed checkbox change
	$( "#jobIsTaxed" ).on( "click", function() {
	  	calculateJobTotal();
	});
	//job line total change
	$( ".garment_part" ).on( "change", function() {
	  	calculateJobTotal();
	});
}

var addCostOfGoodsPercentageListeners = function(){
	$( "#jobTotal" ).on( "change", function() {
	  	calculateCostOfGoodsPercentage();
	});
}


$( document ).ready(function() {
	$('#jobStatusDropdown .dropdown-toggle').attr('href', '#jobStatusDropdown');
	$("#jobStatusDropdown .dropdown-menu li a").click(function(){
	  var group = $(this).parents(".input-group");
	  group.find('.selection').text($(this).text());
	  var listItem = $(this).parent();
	  var statusId = listItem.data('status-id');
	  listItem.siblings().removeClass('active');
	  listItem.addClass('active');
	  group.children('*[type="hidden"]').val(statusId);
	});
	addAutoTotalListeners();
	addCostOfGoodsPercentageListeners();
	setGrandTotal(parseFloat($('#jobTotal').val().replace(/,/g, '')));
});