<div class="md-modal full-width md-dynamicmodal md-close-by-overlay" data-action="" id="modal-cash_management">
    <div class="md-content woocommerce">
        <h1><span class="title"></span> <span class="md-close"></span></h1>
        <div id="cash_management_details" class="col3-set">
            <input type="text" name="amount" onkeyup="amount_validation(this);" onchange="to_float(this);" placeholder="<?php _e('Amount e.g, 50.00', 'wc_point_of_sale') ?>">
            <input type="text" name="note" placeholder="<?php _e('Type to add a note', 'wc_point_of_sale') ?>">
        </div>
        <div class="wrap-button">
            <button class="button button-primary wp-button-large alignright" type="button" id="add-cash-action" disabled>
                <?php _e('Save Customer', 'wc_point_of_sale'); ?>
            </button>
        </div>
    </div>
</div>
<div class="md-overlay"></div>
<div class="md-overlay-prompt"></div>