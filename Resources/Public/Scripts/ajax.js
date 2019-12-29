// content consent
(function($) {
	$(function() {

		$('.ajaxSubmit').on('click', function(event) {
			var $submit = $(this),
				uri = $submit.data('ajaxuri'),
				currentRecord = $submit.val(),
				lazyload = jQuery.parseJSON(TYPO3.settings.T3SB['lazyLoad']);

			if ($('#preloader-'+currentRecord).length > 0) {
				$('#c'+currentRecord).css('position','relative');
				$('#preloader-'+currentRecord).css('display','block');
			}

			$.ajax(
				uri,
				{
					'type': 'post',
					'data': {currentRecord: currentRecord}
				}
			).done(function(result) {

				$('#ajax-result-'+currentRecord).removeClass('px-3');
				$('#ajax-result-'+currentRecord).html(result);

				if ($('#preloader-'+currentRecord).length > 0) {
					$('#preloader-'+currentRecord).css('display','none');
					$('#c'+currentRecord).removeAttr('style');
				}

				if ( lazyload > 0 ) {
					new LazyLoad({
						elements_selector: ".lazy",
						threshold: 0
					});
				}

			});
		});
	});
})(jQuery);
