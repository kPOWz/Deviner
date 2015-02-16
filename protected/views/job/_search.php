<form id='form-job-search' class='gus-input-search' method='GET'>
	<div class="form-group">
		<div class="input-group-lg input-group">
		
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
		    	'class'=>'form-control',
				'id'=>'job-search-box',
				'role'=>'search',
	    		'placeholder'=>'Search by client or company name',
		    	'spellcheck'=>'false',
		    	'results'=>'5',	    		
		    	),
		   ));?>
	   	
		   <span class="input-group-btn">
			   	<button class="btn btn-primary" name="yt0" type="button">
			   	<span class="glyphicon glyphicon-search"></span>
			   	</button>
		   </span>
	   	</div>
	   <input type="hidden" id="search-result-job-id" name="id" >
	</div>
	 
</form>
<!-- search-form -->