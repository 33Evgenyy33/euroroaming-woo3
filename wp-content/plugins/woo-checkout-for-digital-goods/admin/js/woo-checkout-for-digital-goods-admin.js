jQuery(document).ready(function() {
    jQuery('#selecctall').click(function(event) {  //on click 
        if (this.checked) { // check select status
            jQuery('.woo_chk').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"               
            });
        } else {
            jQuery('.woo_chk').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                       
            });
        }
    });
    jQuery("#wcf_dialog").dialog({
        modal: true, title: 'Subscribe To Our Newsletter', zIndex: 10000, autoOpen: true,
        width: '400', resizable: false,
        position: {my: "center", at: "center", of: window},
        dialogClass: 'dialogButtons',
        buttons: [
            {
                id: "Delete",
                text: "YES",
                click: function() {
                    // $(obj).removeAttr('onclick');
                    // $(obj).parents('.Parent').remove();
                    var email_id = jQuery('#txt_user_sub_wcf').val();
                    var data = {
                        'action': 'add_plugin_user_wcf',
                        'email_id': email_id
                    };
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, data, function(response) {
                        jQuery('#wcf_dialog').html('<h2>You have been successfully subscribed');
                        jQuery(".ui-dialog-buttonpane").remove();
                    });
                }
            },
            {
                id: "No",
                text: "No, Remind Me Later",
                click: function() {

                    jQuery(this).dialog("close");
                }
            },
        ]
    });
    jQuery("div.dialogButtons .ui-dialog-buttonset button").removeClass('ui-state-default');
    jQuery("div.dialogButtons .ui-dialog-buttonset button").addClass("button-primary woocommerce-save-button");

});