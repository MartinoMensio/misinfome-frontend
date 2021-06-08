// Script to find all IFRAMES on a page and, if they are of a certain media type, wrap them in a DIV so that they can be styled to be responsive
/* global $ */
/*eslint-env browser*/
$(document).ready(function(){
	"use strict";
	$('iframe[src*="youtube.com"]').wrap('<div class="youtubeWrapper" />'); 
	$('iframe[src*="vimeo.com"]').wrap('<div class="vimeoWrapper" />');
	$('iframe[src*="instagram.com"]').wrap('<div class="instagramWrapper" />');
});


// contact
$(document).ready(function($){
	$('.contact-form input.name').attr('placeholder', 'Full Name');
	$('.contact-form input.email').attr('placeholder', 'Email');
	$('.contact-form textarea.textarea').attr('placeholder', 'Message');
});

// Tooltips Initialization
$(document).ready(function() {
    "use strict";
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
});

$(window).scroll(function(){
    if ($(window).scrollTop() > 500) {
        $('#logo').css('visibility','visible');
        //$('#main-nav').css('top','0px');
    } else {
        $('#logo').css('visibility','hidden');
        //$('#main-nav').css('top','100px');
    }
});

// Animate Search Suggestions
var terms = $("ul.animate-text li");
function rotateTerm() {
    var ct = $("#rotate").data("term") || 0;
    // console.log(terms.eq([ct]).text());
    $("#rotate").data("term", ct == terms.length -1 ? 0 : ct + 1).text(terms.eq([ct]).text()).fadeIn(800).delay(5000).fadeOut(800, rotateTerm);
}
$(rotateTerm);

// Animate Counters
$('.counter').countUp();