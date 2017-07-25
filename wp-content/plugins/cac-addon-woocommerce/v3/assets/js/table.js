
jQuery( document ).ready( function( $ ) {
	$( 'table.wp-list-table td' ).on( 'ajax_column_value_ready', function() {

		// Re-init WC tooltip after column contents has been retrieved by ajax
		$( document.body ).trigger( 'init_tooltips' );
	} );
});