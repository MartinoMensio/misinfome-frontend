$(document).ready(function(){
	//jQuery for page scrolling feature - requires jQuery Easing plugin
	$(function() {
		$('a.page-scroll').bind('click', function(event) {
			$('a').each(function () {
				$(this).removeClass('active');
			})
			$(this).addClass('active');
			
			var $anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top-130
			}, 1200, 'easeInOutExpo');
			event.preventDefault();
		});
	});
	// stop page scroll when inside scrollable section
	$('.dataTables_scrollBody').bind('mousewheel DOMMouseScroll', function(e){
		var e0 = e.originalEvent, delta = e0.wheelDelta || -e0.detail;	
		this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
		e.preventDefault();
	});
});