<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output export-import dialog
 */

?>
<div class="us-hb-window for_export_import">
	<div class="us-hb-window-h">
		<div class="us-hb-window-header">
			<div class="us-hb-window-title"><?php _e( 'Header Export / Import', 'us' ) ?></div>
			<div class="us-hb-window-closer" title="<?php echo us_translate( 'Close' ) ?>"></div>
		</div>
		<div class="us-hb-window-body usof-container">
			<div class="usof-form-row type_transfer desc_2">
				<div class="usof-form-row-field">
					<div class="usof-form-row-control">
						<textarea></textarea>
					</div>
					<div class="usof-form-row-desc">
						<div class="usof-form-row-desc-icon"></div>
						<div class="usof-form-row-desc-text"><?php _e( 'You can export the saved Header by copying the text inside this field. To import another Header replace the text in this field and click "Import Header" button.', 'us' ) ?></div>
					</div>
					<div class="usof-form-row-state"><?php echo us_translate( 'Invalid data provided.' ) ?></div>
				</div>
			</div>
		</div>
		<div class="us-hb-window-footer">
			<div class="us-hb-window-btn for_close"><span><?php echo us_translate( 'Close' ) ?></span></div>
			<div class="us-hb-window-btn for_save"><span><?php _e( 'Import Header', 'us' ) ?></span></div>
		</div>
	</div>
</div>
