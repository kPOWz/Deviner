var ENCOURAGEMENT = (function(){
	var encourage = [
		"Keep doin' whatcha do",
		"Fo' shizzle ur bizzle",
		"Good times",
		"Let's grab another skateboard and, like, do it again",
		"Groovy is as groovy does",
		"Radical stuff",
		"Zang",
		"Yippie-kai-yai-yeay",
		"Roughest, toughest, he-man stuffest hombre’"
	]

	return {
	    random : function(){
	    	return encourage[Math.floor(Math.random() * encourage.length)];
	    }
  	};

})();

$( document ).ready(function() {
	$.each($('[name="encourage"]'), function(idx, val){ $( this ).text(ENCOURAGEMENT.random());;});
  });