/**
 * @file
 * Placeholder file for custom sub-theme behaviors.
 *
 */
(function ($, Drupal) {

	/**
	* Use this behavior as a template for custom Javascript.
	*/
	Drupal.behaviors.flar = {
		attach: function (context, settings) {
			$('.search-icon').click(function(e) {
				e.preventDefault();
				$('#block-flar-search').toggleClass('active');
				$("#search-block-form #edit-keys").focus();
			});

			if (Foundation.MediaQuery.is('small only')) {
				relocateView();
				$(window).resize(function() {
				    relocateView();
				});

				function relocateView() {
				  if ($(window).width() < 640) {console.log('ok');
				    $('#block-flar-search').insertAfter('.mm-panels');
				  }else{}
				}
			}
		}
	};


})(jQuery, Drupal);
