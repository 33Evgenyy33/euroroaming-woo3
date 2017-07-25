( function( $ ) {
	"use strict";

	var Dimensions = function( options ) {
		this.init( 'dimensions', options, Dimensions.defaults );
	};

	$.fn.editableutils.inherit( Dimensions, $.fn.editabletypes.abstractinput );

	$.extend( Dimensions.prototype, {
		activate : function() {
			this.$input.find( 'input:first' ).focus();
		},

		value2input : function( value ) {
			if ( !value ) {
				return;
			}
			this.$input.filter( '[name="length"]' ).val( value.length );
			this.$input.filter( '[name="width"]' ).val( value.width );
			this.$input.filter( '[name="height"]' ).val( value.height );
		},

		input2value : function() {
			return {
				length : this.$input.filter( '[name="length"]' ).val(),
				width : this.$input.filter( '[name="width"]' ).val(),
				height : this.$input.filter( '[name="height"]' ).val()
			};
		}
	} );

	var template;

	template += '<input type="text" class="form-control input-sm small-text" name="length" placeholder="' + acp_woocommerce_i18n.woocommerce.length + '">';
	template += '<input type="text" class="form-control input-sm small-text" name="width" placeholder="' + acp_woocommerce_i18n.woocommerce.width + '">';
	template += '<input type="text" class="form-control input-sm small-text" name="height" placeholder="' + acp_woocommerce_i18n.woocommerce.height + '">';

	Dimensions.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults, {
		tpl : template
	} );

	$.fn.editabletypes.dimensions = Dimensions;
}( window.jQuery ) );

jQuery.fn.cacie_edit_dimensions = function( column, item ) {

	var el = jQuery( this );

	el.cacie_xeditable( {
		type : 'dimensions',
		value : el.cacie_get_value( column, item ),
		validate : function( value ) {
			if ( !cacie_is_float( value.length ) || !cacie_is_float( value.width ) || !cacie_is_float( value.height ) ) {
				return acp_woocommerce_i18n.errors.invalid_floats;
			}
		}
	}, column, item );
};