/* JavaScript Document

--------------------------------
Strukturierter Dialog
Copyright 2012 Adam Kudrna
--------------------------------

*/



// Initialize
$(document).ready(function () {


	// Popovers
	// -------------------------

	$('*[rel="popover"]').popover();


	// Buttons
	// -------------------------

	$('button.arrow-right').append(' <i class="icon-chevron-right"></i>');


	// Scroll to ID
	// -------------------------

	$('.js-scroll').click(function() {
		$('html, body').animate({ scrollTop: $(this.hash).offset().top}, 500);
		return false;
		e.preventDefault();
	});

});
