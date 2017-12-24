( function( $ ) {
	"use strict";

	var WC_Usage = function( options ) {
		this.init( 'wc_usage', options, WC_Usage.defaults );
	};

	$.fn.editableutils.inherit( WC_Usage, $.fn.editabletypes.abstractinput );

	$.extend( WC_Usage.prototype, {
		value2input : function( value ) {

			if ( !value ) {
				return;
			}

			this.$input.find( '[name="usage_limit"]' ).val( value.usage_limit );
			this.$input.find( '[name="usage_limit_per_user"]' ).val( value.usage_limit_per_user );
		},

		input2value : function() {
			return {
				usage_limit : this.$input.find( '[name="usage_limit"]' ).val(),
				usage_limit_per_user : this.$input.find( '[name="usage_limit_per_user"]' ).val()
			};
		}
	} );

	var template = '';

	template += '<div>';

	template += '<div>';
	template += '<label>' + acp_woocommerce_i18n.woocommerce.usage_limit_per_coupon + '</label>';
	template += '<input type="text" class="form-control input-sm small-text" name="usage_limit">';
	template += '</div>';

	template += '<div>';
	template += '<label>' + acp_woocommerce_i18n.woocommerce.usage_limit_per_user + '</label>';
	template += '<input type="text" class="form-control input-sm small-text" name="usage_limit_per_user">';
	template += '</div>';

	template += '</div>';

	WC_Usage.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults, {
		tpl : template
	} );

	$.fn.editabletypes.wc_usage = WC_Usage;
}( window.jQuery ) );

jQuery.fn.cacie_edit_wc_usage = function( column, item ) {

	var el = jQuery( this );

	el.cacie_xeditable( {
		type : 'wc_usage',
		value : el.cacie_get_value( column, item )
	}, column, item );
};