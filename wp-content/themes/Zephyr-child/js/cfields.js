// This file named "cfields.js" goes in a subfolder "js" of your active child theme or theme

jQuery(document).ready(function($){

    // Common Serial ID field
    if(! $("#billing_ser_id_field").hasClass("validate-required") ){
        $("#billing_ser_id_field").addClass("validate-required");
    }


    // The 4 Fields to hide at start (if not "Persoana Juridica")
    if($("#billing_status option:selected").val() == "1"){
        $('#billing_company_field').hide(function(){
            $(this).removeClass("validate-required");
            $(this).removeClass("woocommerce-validated");
            $('#billing_company').val("no");
        });
        $('#billing_bt_id_field').hide(function(){
            $(this).removeClass("validate-required");
            $(this).removeClass("woocommerce-validated");
            $('#billing_bt_id').val("no");
        });
        $('#billing_ib_id_field').hide(function(){
            $(this).removeClass("validate-required");
            $(this).removeClass("woocommerce-validated");
            $('#billing_ib_id').val("no");
        });
        $('#billing_cf_id_field').hide(function(){
            $(this).removeClass("validate-required");
            $(this).removeClass("woocommerce-validated");
            $('#billing_cf_id').val("no");
        });
    }

    // Action with the selector (Showing/hiding and adding/removing classes)
    $("#billing_status").change(function(){
        // For "Persoana Juridica"
        if($("#billing_status option:selected").val() == "2")
        {
            $('#billing_company_field').show(function(){
                $(this).addClass("validate-required");
                $('#billing_company').val("");
            });
            $('#billing_bt_id_field').show(function(){
                $(this).children('label').append( ' <abbr class="required" title="required">*</abbr>' );
                $(this).addClass("validate-required");
                $('#billing_bt_id').val("");
            });
            $('#billing_ib_id_field').show(function(){
                $(this).children('label').append( ' <abbr class="required" title="required">*</abbr>' );
                $(this).addClass("validate-required");
                $('#billing_ib_id').val("");
            });
            $('#billing_cf_id_field').show(function(){
                $(this).children('label').append( ' <abbr class="required" title="required">*</abbr>' );
                $(this).addClass("validate-required");
                $('#billing_cf_id').val("");
            });
        }
        // For "Persoana Fizica"
        else if($("#billing_status option:selected").val() == "1")
        {
            $('#billing_company_field').hide(function(){
                $(this).removeClass("validate-required");
                $(this).removeClass("woocommerce-validated");
                $('#billing_company').val("no");
            });
            $('#billing_bt_id_field').hide(function(){
                $(this).children("abbr.required").remove();
                $(this).removeClass("validate-required");
                $(this).removeClass("woocommerce-validated");
                $('#billing_bt_id').val("no");
            });
            $('#billing_ib_id_field').hide(function(){
                $(this).children("abbr.required").remove();
                $(this).removeClass("validate-required");
                $(this).removeClass("woocommerce-validated");
                $('#billing_ib_id').val("no");
            });
            $('#billing_cf_id_field').hide(function(){
                $(this).children("abbr.required").remove();
                $(this).removeClass("validate-required");
                $(this).removeClass("woocommerce-validated");
                $('#billing_cf_id').val("no");
            });
        }

    });

});