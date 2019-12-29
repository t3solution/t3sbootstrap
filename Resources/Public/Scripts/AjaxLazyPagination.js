$(document).ready(function () {

    $('.news').on('click', '.page-navigation a', function (e) {

		e.preventDefault();

	    $('#news-spinner').css('display','block');

		var ajaxUrl = $(this).attr('href');

        if (ajaxUrl !== undefined && ajaxUrl !== '') {

            e.preventDefault();
            var container = 'news-container-' + $(this).data('container');

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                success: function (result) {
                    var ajaxDom = $(result).find('#' + container);

                    $('#' + container).replaceWith(ajaxDom);

					// with lazyload only
					new LazyLoad({
						elements_selector: ".lazy",
						threshold: 0
					});

					$('#news-spinner').delay(350).fadeOut('slow');

                }
            });
        }
    });
});