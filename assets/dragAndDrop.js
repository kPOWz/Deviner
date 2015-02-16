/*Implementation of HTML5 drag & drop API for GUS calendar */
(function(){

	var updatePrintDateUrl;
	var validateNewPrintDateUrl;
	var dragSrcEl = null;
	var calendarDays;
	var jobs;
	var dragging = [];
	
	var handleDragStart = function(e) {
		  // Target (this) element is the source node.			
		  dragSrcEl = $(this).parent();
		  
		  e.dataTransfer.effectAllowed = 'move';
		  e.dataTransfer.setData('text/plain', e.target.id);
		  dragging.push(e.target.id);
		}
	
	var handleDragOver = function(e) {
		  if (e.preventDefault) {
		    e.preventDefault(); // Necessary. Allows us to drop.
		  }
	
		  e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
		
		  return false;
		}
	
	var handleDragEnter = function(e) {
		// this / e.target is the current hover target.
		var jobId = e.dataTransfer.getData('text/plain') || dragging[0];
		
		if (dragSrcEl[0] != this) {
			var targetId = this.getAttribute('id');
			target = this; //TODO: anti-pattern
			
			//if has class class="ui-cal-items" we've entered a valid target element and drop is allowed
			if(targetId && $('#'+targetId).hasClass('ui-cal-items')) {
				$.ajax({
					url: validateNewPrintDateUrl,
					type: "GET",
					dataType: 'json',
					data: {
							"newPrintDate": targetId,
							"id": jobId
						},
					success: function(data){
						if(data.VALID){
							//if valid target date, set class to correctly qualify :drag-over selector
							//event.stopPropagation();
							if (e.preventDefault) {
							    e.preventDefault(); // Necessary. Allows us to drop.
							  }
							if (e.preventDefault) {
							    e.preventDefault(); // Necessary. Allows us to drop.
							  }	
							e.dataTransfer.dropEffect = 'move';
							target.classList.add('valid-target');
						}
						else{
							//if invalid target date, set class to correctly qualify :drag-over selector
							e.dataTransfer.dropEffect = 'none';
							target.classList.add('invalid-target');
						}
					}
				});
			}
		}
	}
		
	var handleDragLeave = function(e) {	
		// this / e.target is previous target element.
		this.classList.remove('valid-target');
		//TODO: remove invalid-target
		this.classList.remove('invalid-target');
	}
	
	var handleDrop = function(e) {
		// this / e.target is current target element.
		
	  if (e.stopPropagation) {
	    e.stopPropagation(); // stops the browser from redirecting.
	  }
	  // stops the browser from redirecting off to the text.
	  if (e.preventDefault) {
	    e.preventDefault(); 
	  }
		 
	  if (dragSrcEl != this) {
		  	var targetId = this.getAttribute('id');
			
		  	//get the stored job id
		  	var jobId = e.dataTransfer.getData('text/plain') || dragging[0];
		  
			$.ajax({
				url: updatePrintDateUrl,
				type: "POST",
				dataType: 'json',
				data: {
						"newPrintDate" : targetId,
						"id": jobId,
					},
				success: function(data){
						if(data.SAVED){
							//if valid target date, instruct the browser to allow the drop
							 
							//add it to the drop element
							//e.target.appendChild(theDraggedElement);
							//get the element
							var theDraggedElement = document.getElementById(jobId);
							e.target.appendChild(theDraggedElement);
							//TODO: set flash ?
						}
						else{
							//if invalid target date, alert user
							alert('Cannot set Print Date beyond current Due Date of ' + data.DUEDATE);
							//alert('Cannot set Print Date beyond current Due Date of ');
						}
				},
				error: function(request, type, errorThrown){
					alert('Something went wrong and we couldn\'t update the Print Date.');
				}
			});
		  }
		  return false;
		}
	
	var handleDragEnd = function(e) {
	    // this/e.target is the source node.
		
		  [].forEach.call(calendarDays, function (day) {
		    day.classList.remove('valid-target');
		    //TODO: remove invalid-target
		    day.classList.remove('invalid-target');
		  });
		dragging.splice(dragging.indexOf(e.target.id), 1);
		}
	
	
	initCalendar = function(validateUrl,updateUrl){
		validateNewPrintDateUrl = validateUrl;
		updatePrintDateUrl = updateUrl;
		
		calendarDays = document.querySelectorAll('.cal_container .ui-cal-items');
		[].forEach.call(calendarDays, function(day) {
		  day.addEventListener('dragenter', handleDragEnter, false);
		  day.addEventListener('dragover', handleDragOver, false);
		  day.addEventListener('drop', handleDrop, false);
		  day.addEventListener('dragleave', handleDragLeave, false);
		  day.addEventListener('dragend', handleDragEnd, false);
		});
		
		jobs = document.querySelectorAll('.ui-cal-item');
		[].forEach.call(jobs, function(job) {
		  job.addEventListener('dragstart', handleDragStart, false);
		});
	
	};
	
})();