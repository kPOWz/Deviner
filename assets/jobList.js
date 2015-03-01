function animateStatusChangeRows(rows){
	rows.animate({backgroundColor: '#FFF'}, 1000, 
		function(){ 
					$(this).removeClass('bg-success').attr('style',''); 
				});
}

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
			var row = $(selector).closest("tr");
			row.removeClass('selected');
			row.addClass('bg-success');
			if(targetTab[0] != null){
				targetTab.find('tbody').prepend(row.remove());
			}else{				
				animateStatusChangeRows(row);
			}						
		}
	});
}

jQuery(document).ready(function($) {
   $( ".nav-tabs a" ).on( "click", function() {
           //only fade out succuess highlight if has been seen
           var status = $(this).parent().data('status');
           var successRows = $('.tab-content').children('#job-content-'+status)
                   .find('.bg-success');
           animateStatusChangeRows(successRows);
   });
    $(".row-clickable").click(function() {
        window.document.location = $(this).data("href");
	});
});