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


var calculateSalesTax = function(subTotal){
	var taxRate = 0;
	if($('#jobIsTaxed').prop('checked')){
		taxRate = parseInt($('#tax_rate').val(), 10);
		taxRate = (typeof taxRate  === "number" && !isNaN(taxRate)) ? taxRate : 0;		
	}
	return parseFloat((taxRate/100 * subTotal).toFixed(2));
}

var setGrandTotal = function(subTotal){
	var gTotal = subTotal + calculateSalesTax(subTotal);
	$('#jobTotal').val(gTotal.toFixed(2));
}

var autoTotalJob = function(){
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

var addAutoTotalListeners = function(){
	//any fee input change
	$( ".auto_quote .part" ).on( "change keyup", function() {
	  	autoTotalJob();
	});
	//is taxed checkbox change
	$( "#jobIsTaxed" ).on( "click", function() {
	  	autoTotalJob();
	});
	//job line total change
	$( ".garment_part" ).on( "change", function() {
	  	autoTotalJob();
	});
}


$( document ).ready(function() {
	$('#jobStatusDropdown .dropdown-toggle').attr('href', '#jobStatusDropdown');
	$("#jobStatusDropdown .dropdown-menu li a").click(function(){
	  var group = $(this).parents(".input-group");
	  group.find('.selection').text($(this).text());
	  var statusId = $(this).parent().data('status-id');
	  group.children('*[type="hidden"]').val(statusId);
	});
	addAutoTotalListeners();
	setGrandTotal(parseFloat($('#jobTotal').val()));
});