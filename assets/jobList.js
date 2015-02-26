function statusChanged(selector,jobId){
	var status = $(selector).val(); 
	$.ajax({
		url: 'status',
		dataType: "json",
		data: {
			status: status,
			id: jobId,
		},
		type: 'POST',
		success: function(data){
			$('[name=salesNumber]').text(data.sales);
			$('[name=salesPercentage]').text(data.cogsPercentage);
			
			var targetTab = $(selector).parents('.tab-content').children('#job-content-'+status);
			var removed = $(selector).closest("tr").remove();
			removed.removeClass('selected');
			removed.addClass('bg-success');
			targetTab.find('tbody').prepend(removed);
		}
	});
}

$( ".nav-tabs a" ).on( "click", function() {
	//only fade out succuess highlight if has been seen
	var status = $(this).parent().data('status');
	var successRows = $('.tab-content').children('#job-content-'+status)
		.find('.bg-success');
	successRows.animate({backgroundColor: '#FFF'}, 1000, 
		function(){ 
					$(this).removeClass('bg-success').attr('style',''); 
				});
});