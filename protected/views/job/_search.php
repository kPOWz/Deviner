<form id='form-job-search' class='search row' method='GET'>
	<label>
		<span>Search:</span>
		  <?php echo CHtml::script("
		      function split(val) {
		       	return val.split(/,\s*/);
		      }
		    ")?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		    'model'=>$model,
			'attribute'=>'NAME',
		    'source'=>"js:function(request, response) {
		       $.getJSON('".$this->createUrl('job/search')."', {
		         term: request.term
		       }, response);
		       }",
		    'options'=>array(
		      	'delay'=>300,
		      	'minLength'=>3,
		      	'showAnim'=>'fold',
		      	'select'=>"js:function(event, ui) {
			    		//set hidden value using ui.item.id
			    		$('#search-result-job-id').val(ui.item.id);
			    		//set form action
			    		$('#form-job-search').attr('action', '".CHtml::normalizeUrl(array('job/searchResult'))."');
		        	}",
		    	'close'=>"js:function(event, ui) {
			    		//sumbit form
	    				$('#form-job-search').submit();
		    		}",
		    ),
		    'htmlOptions'=>array(
		      	'size'=>'40',
		    	'class'=>'search row',
				'id'=>'job-search-box',
				'role'=>'search',
	    		'placeholder'=>'job name',
		    	'spellcheck'=>'false',
		    	'results'=>'5',	    		
		    	),
		   ));?>
	   </label>
	   <input type="hidden" id="search-result-job-id" name="id" >
	 
</form>
<!-- search-form -->