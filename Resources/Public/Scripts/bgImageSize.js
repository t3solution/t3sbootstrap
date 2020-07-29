/* parallax*/
(function($){var ua=$.browser;var $window=$(window);var windowHeight=$window.height();$window.resize(function(){windowHeight=$window.height();});
$.fn.parallax=function(xpos,speedFactor,outerHeight){var $this=$(this);var getHeight;var firstTop;var paddingTop=0;$this.each(function(){firstTop=$this.offset().top;});if(outerHeight){getHeight=function(jqo){return jqo.outerHeight(true);};}else{getHeight=function(jqo){return jqo.height();};}
if(arguments.length<1||xpos===null)xpos="50%";if(arguments.length<2||speedFactor===null)speedFactor=0.1;if(arguments.length<3||outerHeight===null)outerHeight=true;var pattern=/Android|webOS|iPhone|iPad|iPod|BlackBerry/i;function update(){if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)){$this.each(function(){$this.css('backgroundPosition',"top left");$this.css('backgroundAttachment',"scroll");});return;}
var pos=$window.scrollTop();$this.each(function(){var $element=$(this);var top=$element.offset().top;var height=getHeight($element);if(top+height<pos||top>pos+windowHeight){return;}
$this.css('backgroundPosition',xpos+" "+Math.round((firstTop-pos)*speedFactor)+"px");});}
$window.bind('scroll',update).resize(update);update();};
})(jQuery);

var mobile = false;
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	mobile = true;
}

jQuery(function($){
	function addHeight(objKey) {
		var overlay = $('#'+objKey).next('.card-img-overlay');
		if (overlay.length) {
			var totalHeight = 0;
			var addHeight = 0;
			var outerHeight = 0;
			$.each( TYPO3.settings.ADDHEIGHT, function( addKey, addValue ) {
				if (objKey == 'bg-img-'+addKey) {
					addHeight = parseInt(addValue);
				}
			});
			$.each( overlay, function( objKeyOverlay, objValueOverlay ) {
				$(objValueOverlay).children().each(function(){
					if ($(this).hasClass('container')) {
						var container = $(this);
						container.children().each(function(){
							outerHeight = parseInt($(this).outerHeight(true));
							totalHeight = totalHeight + outerHeight;
						});
					} else {
						outerHeight = parseInt($(this).outerHeight(true));
						totalHeight = totalHeight + outerHeight;
					}
				});
			});
			totalHeight = totalHeight + addHeight;
			$('#'+objKey).css('min-height', totalHeight+'px');
		}
	}

	$('.parallax, .multiple-parallax').each(function(){
		var speed='0.1';
		if(typeof $(this).attr('data-speed')!='undefined' && $(this).attr('data-speed')!=''){
			speed=$(this).attr('data-speed');
		}
		$(this).parallax('50%',speed);
	});

	if ($(".enableAutoheight").length) {
		$.each( $(".enableAutoheight"), function( key, value ) {
			addHeight($(this).attr('id'));
		});
	}

	if (mobile){
		$('.background-image').removeClass('background-fixed');
	}
});
