//mike font resizer
$(document).ready(function() { 
	var elements = '.boxContent, .boxContent span, .boxContent p, .boxContent h1, .boxContent h2, .boxContent h3, .boxContent h4, .boxContent h5, .boxContent li, .boxContent div, .boxContent a'
	var originalFontSize = $('.boxContent').css('font-size');
	
	//reset font size
	$("#resetFont").click(function(){
		$(elements).css('font-size', originalFontSize);
		$('#fontCount').text(10);
	});
	
	// Increase Font Size
	$("#increaseFont").click(function(){
	  var fontCount=parseInt($('#fontCount').text());
	  if(fontCount<20){
		$(elements).each(function(i) {
			var currentFontSize = $(this).css('font-size');
			var currentFontSizeNum = parseInt(currentFontSize);
			var newFontSize = currentFontSizeNum+2;
			//if(newFontSize<24){
				$(this).css('font-size', newFontSize);
			//}
		});
		 $('#fontCount').text(fontCount+1);
	  }
	return false;
	});
	
	// Decrease Font Size
	$("#decreaseFont").click(function(){
	  var fontCount=parseInt($('#fontCount').text());
	  if(fontCount>5){
		  $(elements).each(function(i) {
			var currentFontSize = $(this).css('font-size');
			var currentFontSizeNum = parseInt(currentFontSize);
			var newFontSize = currentFontSizeNum-2;
			//if(newFontSize>8){
				$(this).css('font-size', newFontSize);
			//}
		  });
		  $('#fontCount').text(fontCount-1);
	  }
	  
	return false;
	});
});