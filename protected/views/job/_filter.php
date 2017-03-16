<script>
    function updateGridView(term, status) {
        $.fn.yiiGridView.update('job-grid-status-'.concat(status), {data: {term: term, status: status}});
    }

    $( document ).ready(function() {	
		$( "#job-filter-box" ).on("input", function(event) {
			const term = event.target.value;
			if(term.length > 2){
				const activeStatus = $('#job-tabs li.active').data('status')
				updateGridView(term, activeStatus);
			}
		});
		$( "#job-filter-box" ).on("keypress", function(event) {
			const term = event.target.value;
			if(event.keyCode === 13){
				event.preventDefault();
				const activeStatus = $('#job-tabs li.active').data('status')
				updateGridView(term, activeStatus);
			}
		});
	});
</script>
<form id='form-job-filter' class='gus-input-search' method='GET'>
	<div class="form-group">
		<div class="input-group-lg input-group gus-input-group">
			<input size="40" class="form-control ui-autocomplete-input" id="job-filter-box" role="search" 
				placeholder="Filter by client, company or job name" spellcheck="false" results="5" name="Job[NAME]" 
				type="text" autocomplete="off">
			<span class="bg-primary input-group-addon">
			   	<span class="glyphicon glyphicon-filter"></span>
	   		</span>
	   	</div>
	   <input type="hidden" id="search-result-job-id" name="id" />
	</div>	 
</form>
