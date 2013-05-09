//call function when drag of a draggable=true element starts
function dragIt(theEvent) {
	//tell the browser what to drag	
	theEvent.dataTransfer.setData("Text", theEvent.target.id);
}

//call function when drug element drops
function dropIt(theEvent) {
	//get a reference to the element being dragged
	var theData = theEvent.dataTransfer.getData("Text");

	//get the element
	var theDraggedElement = document.getElementById(theData);
	
	//add it to the drop element
	theEvent.target.appendChild(theDraggedElement);
	
	//instruct the browser to allow the drop
	theEvent.preventDefault();
}