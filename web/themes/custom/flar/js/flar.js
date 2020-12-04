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
			/* Bloque Buscador Header */
			$('.search-icon').click(function(e) {
				e.preventDefault();
				$('#block-flar-search').toggleClass('active');
				$("#search-block-form #edit-keys").focus();
			});
			/* Bloque Buscador Menú Móvil */
			if (Foundation.MediaQuery.is('small only')) {
				relocateView();
				$(window).resize(function() {
				    relocateView();
				});
				function relocateView() {
				  if ($(window).width() < 640) {
				    $('#block-flar-search').insertAfter('.mm-panels');
				  }else{}
				}
			}
			/* Abir en nueva pestaña los archivos */
			jQuery( "a" ).click(function() {
				var href = jQuery( this ).attr('href');
			  if(href.indexOf('.pdf') > -1 || href.indexOf('.doc') > -1 || href.indexOf('.docx') > -1 || href.indexOf('.xls') > -1 || href.indexOf('.xlsx') > -1){
			  	jQuery( this ).attr('target', '_blank');
			  }
			});
			/* Breadcumb Buscador */
			var url = window.location.toString();
			if(url.indexOf('/search/node') > -1){
				jQuery('nav ul.breadcrumbs li.current a').remove();
				jQuery('nav ul.breadcrumbs li.current').remove();
			}
			$('#block-flar-main-menu').css("z-index","99999");
			$(".btn-border-r").css("border-radius", "20px");

			let param_url = "";
			let location = window.location.pathname;
			let searchParams = new URLSearchParams(window.location.search)
			param_url = searchParams.get('url')
			if(param_url == null){
				location.href="/investigaciones";
			}
			if (location.indexOf('documento') >= 0){
				$("#iframe_doc").attr('src', param_url)
			}else{
				location.href="/investigaciones";
			}
		}
	};


})(jQuery, Drupal);
