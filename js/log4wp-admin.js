jQuery(document).ready(function () {


	jQuery('.log4wp-page-view-logs .row-large-message .entry-view-more').click(function(e) {
		e.preventDefault();
		jQuery(e.target).parent().parent().removeClass('row-large-message');
	});

});