jQuery( function( $ ) {

	// Add condition
	$( '#was_conditions' ).on( 'click', '.condition-add', function() {

		var data = {
			action: 'was_add_condition',
			group: $( this ).attr( 'data-group' ),
			nonce: was.nonce
		};

		// Loading icon
		var loading_icon = '<div class="was-condition-wrap loading"></div>';
		$( '.condition-group-' + data.group ).append( loading_icon ).children( ':last' ).block({ message: null, overlayCSS: { background: '', opacity: 0.6 } });

		$.post( ajaxurl, data, function( response ) {
			$( '.condition-group-' + data.group + ' .was-condition-wrap.loading' ).first().replaceWith( function() {
				return $( response ).hide().fadeIn( 'normal' );
			});
		});

	});

	// Delete condition
	$( '#was_conditions' ).on( 'click', '.condition-delete', function() {

		if ( $( this ).closest( '.condition-group' ).children( '.was-condition-wrap' ).length == 1 ) {
			$( this ).closest( '.condition-group' ).fadeOut( 'normal', function() { $( this ).remove();	});
		} else {
			$( this ).closest( '.was-condition-wrap' ).fadeOut( 'normal', function() { $( this ).remove(); });
		}

	});

	// Add condition group
	$( '#was_conditions' ).on( 'click', '.condition-group-add', function() {

		var condition_group_loading = '<div class="condition-group loading"></div>';

		// Display loading icon
		$( '.was_conditions' ).append( condition_group_loading ).children( ':last').block({ message: null, overlayCSS: { background: '', opacity: 0.6 } });

		var data = {
			action: 'was_add_condition_group',
			group: 	parseInt( $( '.condition-group' ).length ),
			nonce: 	was.nonce
		};

		// Insert condition group
		$.post( ajaxurl, data, function( response ) {
			$( '.condition-group ~ .loading' ).first().replaceWith( function() {
				return $( response ).hide().fadeIn( 'normal' );
			});
		});

	});

	// Update condition values
	$( '#was_conditions' ).on( 'change', '.was-condition', function () {

		var loading_wrap = '<span style="width: 30%; border: 1px solid transparent; display: inline-block;">&nbsp;</span>';
		var data = {
			action: 		'was_update_condition_value',
			id:				$( this ).attr( 'data-id' ),
			group:			$( this ).attr( 'data-group' ),
			condition: 		$( this ).val(),
			nonce: 			was.nonce
		};

		var replace = '.was-value-wrap-' + data.id;

		$( replace ).html( loading_wrap )
			.block({ message: null, overlayCSS: { background: '', opacity: 0.6 } });

		$.post( ajaxurl, data, function( response ) {
			$( replace ).replaceWith( response );
		});

		// Update condition description
		var description = {
			action:		'was_update_condition_description',
			condition: 	data.condition,
			nonce: 		was.nonce
		};

		$.post( ajaxurl, description, function( description_response ) {
			$( replace + ' ~ .was-description' ).replaceWith( description_response );

			// Tooltip
			$( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip({ 'attribute': 'data-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 200 });
		})

	});

	/**************************************************************
	 * Overview
	 *************************************************************/

	// Sortable
	$( '.was-table tbody' ).sortable({
		items:					'tr',
		handle:					'.sort',
		cursor:					'move',
		axis:					'y',
		scrollSensitivity:		40,
		forcePlaceholderSize: 	true,
		helper: 				'clone',
		opacity: 				0.65,
		placeholder: 			'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css( 'background-color','#f6f6f6' );
		},
		stop:function(event,ui){
			ui.item.removeAttr( 'style' );
		},
		update: function(event, ui) {

			$table 	= $( this ).closest( 'table' );
			$table.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
			// Update shipping method order
			var data = {
				action:	'was_save_shipping_rates_table',
				form: 	$( this ).closest( 'form' ).serialize(),
				nonce: was.nonce
			};

			$.post( ajaxurl, data, function( response ) {
				$( '.was-table tbody tr:even' ).addClass( 'alternate' );
				$( '.was-table tbody tr:odd' ).removeClass( 'alternate' );
				$table.unblock();
			});
		}
	});

	// Toggle list table rows on small screens
	$( '#advanced_shipping_shipping_methods' ).on( 'click', '.toggle-row', function() {
		$( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	});

});