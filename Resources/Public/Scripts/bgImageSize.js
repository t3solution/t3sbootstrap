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

	function resizeMultipleBgImage(winWidth, objKey, value, key) {

		var raster = '/typo3conf/ext/t3sbootstrap/Resources/Public/Images/raster.png';
		var currentElement = $('#s'+objKey+'.bgImageSize');

		if (currentElement.css('background-image') !== undefined) {
			var currentObj = $('#s'+objKey);
		} else {
			var currentObj = $('#bg-img-'+objKey);
		}

		if ( winWidth < 577 && key == 576 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 576 && winWidth < 769 && key == 768 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 768 && winWidth < 993 && key == 992 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 992 && winWidth < 1201 && key == 1200 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 1200 && winWidth < 1921 && key == 1920 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 1920 && winWidth < 2561 && key == 2560 ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
		if ( winWidth > 2560 && key == 'orig' ) {
			currentObj.css('background-image', 'url(' + raster +'), url(' + value + ')');
		}
	}
	function resizeBgImage(winWidth, objKey, value, key, currentElement) {

		if ($(currentElement).is('body')) {
			var currentObj = $('#page-'+objKey);
		} else {
			if (currentElement.css('background-image') !== undefined) {
				var currentObj = $('#s'+objKey);
			} else {
				var currentObj = $('#bg-img-'+objKey);
			}
		}

		if (currentObj.length) {
	
			if ( winWidth < 577 && key == 576 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 576 && winWidth < 769 && key == 768 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 768 && winWidth < 993 && key == 992 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 992 && winWidth < 1201 && key == 1200 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 1200 && winWidth < 1921 && key == 1920 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 1920 && winWidth < 2561 && key == 2560 ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
			if ( winWidth > 2560 && key == 'orig' ) {
				currentObj.css('background-image', 'url(' + value + ')');
			}
	
		}
	}
	function backgroundSize() {
		var winWidth = $(window).width();
		var enableHeight = false;

		if (TYPO3.settings.BGWRAPPER !== undefined) {
			$.each( TYPO3.settings.BGWRAPPER, function( objKey, objValue ) {
				var currentElement = $('#s'+objKey+'.bgImageSize');

				$.each( TYPO3.settings.ENABLEHEIGHT, function( enableKey, enableValue ) {
					if ( (objKey == enableKey) && enableValue ) {
						enableHeight = true;
					}
				});

				if (jQuery.parseJSON(objValue)[0] !== undefined) {
					//bgColor only -> false
					if (enableHeight) addHeight(objKey, false);

				} else {

					if (enableHeight) addHeight(objKey);
					var $obj = jQuery.parseJSON(objValue);

					$.each( $obj, function( key, value ) {
						$(window).on('resize', function(){
							var winWidth = $(window).width();
							if ( $('.bgImageSize').hasClass('multiple-background-image') || $('.bgImageSize').hasClass('multiple-parallax') ) {
								resizeMultipleBgImage(winWidth, objKey, value, key);
							} else {
								resizeBgImage(winWidth, objKey, value, key, currentElement);
							}
						});
						if ( $('.bgImageSize').hasClass('multiple-background-image') || $('.bgImageSize').hasClass('multiple-parallax') ) {
							resizeMultipleBgImage(winWidth, objKey, value, key);
						} else {
							resizeBgImage(winWidth, objKey, value, key, currentElement);
						}
					});
				}
			});
		}
		if (TYPO3.settings.JUMBOTRON !== undefined) {
			$.each( TYPO3.settings.JUMBOTRON, function( objKey, objValue ) {
				var currentElement = $('#s'+objKey+'.bgImageSize');
				var $obj = jQuery.parseJSON(objValue);
				objKey = $('body').attr('id').split('-')[1];
				$.each( $obj, function( key, value ) {
					$(window).on('resize', function(){
						var winWidth = $('.jumbotron').width();
						resizeBgImage(winWidth, objKey, value, key, currentElement);
					});
					resizeBgImage(winWidth, objKey, value, key, currentElement);
				});
			});
		}
		if (TYPO3.settings.BODY !== undefined && $('body').hasClass("bgi")) {
			$.each( TYPO3.settings.BODY, function( objKey, objValue ) {
				var currentElement = "#"+$('body').attr('id');
				var $obj = jQuery.parseJSON(objValue);
				objKey = $('body').attr('id').split('-')[1];
				$.each( $obj, function( key, value ) {
					$(window).on('resize', function(){
						var winWidth = $(window).width();
						resizeBgImage(winWidth, objKey, value, key, currentElement);
					});
					resizeBgImage(winWidth, objKey, value, key, currentElement);
				});
			});
		}
	}

	function addHeight(objKey, bgimg) {

		bgimg = (typeof b !== 'undefined') ?	bgimg : 1;

		if (bgimg) {
			var overlay = $('#bg-img-'+objKey).next('.card-img-overlay');
		} else {
			var overlay = $('#bg-col-'+objKey).next('.card-img-overlay');
		}

		if (overlay.length) {
			var totalHeight = 0;
			var addHeight = 0;
			var outerHeight = 0;

			$.each( TYPO3.settings.ADDHEIGHT, function( addKey, addValue ) {
				if (objKey == addKey) {
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

			if (bgimg) {
				$('#bg-img-'+objKey).css('min-height', totalHeight+'px');
			} else {
				$('#bg-col-'+objKey).css('min-height', totalHeight+'px');
			}
		}
	}


	$('.parallax, .multiple-parallax').each(function(){
		var speed='0.1';
		if(typeof $(this).attr('data-speed')!='undefined' && $(this).attr('data-speed')!=''){
			speed=$(this).attr('data-speed');
		}
		$(this).parallax('50%',speed);
	});

	backgroundSize();

	if (mobile){
		$('.background-image').removeClass('background-fixed');
	}

});
