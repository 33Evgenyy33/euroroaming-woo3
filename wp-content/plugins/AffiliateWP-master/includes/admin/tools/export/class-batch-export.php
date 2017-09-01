<?php
namespace AffWP\Utils\Batch_Process;

use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Writer_Excel2007;

if ( ! class_exists( '\Affiliate_WP_Export' ) ) {
	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export.php';
}

error_reporting( E_ALL );
ini_set( 'display_errors', true );
ini_set( 'display_startup_errors', true );
//date_default_timezone_set('Europe/London');

if ( PHP_SAPI == 'cli' ) {
	die( 'This example should only be run from a Web Browser' );
}

/**
 * Implements the base batch exporter as an intermediary between a batch process
 * and the base exporter class.
 *
 * @since 2.0
 *
 * @see \Affiliate_WP_Export
 */
class Export extends \Affiliate_WP_Export {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id;

	/**
	 * The file the export data will be stored in.
	 *
	 * @access protected
	 * @since  2.0
	 * @var    resource
	 */
	protected $file;

	/**
	 * The name of the file the export data will be stored in.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filename;

	/**
	 * The export file type, e.g. '.csv'.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filetype;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int|string Step number or 'done'.
	 */
	public $step;

	/**
	 * Whether the the export file is writable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_writable = true;

	/**
	 * Whether the export file is empty.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_empty = false;

	/**
	 * Number of items to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 100;

	/**
	 * Sets up the batch export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 */
	public function __construct( $step ) {

		$upload_dir     = wp_upload_dir();
		$this->filename = 'affiliate-wp-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . $this->filetype;
		$this->file     = trailingslashit( $upload_dir['basedir'] ) . $this->filename;

		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			$this->is_writable = false;
		}

		$this->step = $step;
		$this->done = false;

		if ( has_filter( "affwp_export_per_step_{$this->export_type}" ) ) {
			/**
			 * Filters the number of items to process per step for the given export type.
			 *
			 * The dynamic portion of the hook name, `$this->export_type` refers to the export
			 * type defined in each sub-class.
			 *
			 * @since 2.0
			 *
			 * @param int                               $per_step The number of items to process
			 *                                                    for each step. Default 100.
			 * @param \AffWP\Utils\Batch_Process\Export $this     Exporter instance.
			 */
			$this->per_step = apply_filters( "affwp_export_per_step_{$this->export_type}", $this->per_step, $this );
		}
	}

	/**
	 * Determines if the current user can perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process() {
		return $this->can_export();
	}

	/**
	 * Sets the export headers.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! affwp_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->filename );
		header( "Expires: 0" );
	}

	/**
	 * Retrieves the file that data will be written to.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return string File data.
	 */
	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
	}

	/**
	 * Initiate the export file download.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function export() {
		$all_date1 = $this->get_stat_affil();
		$data          = $all_date1[1];
		$summary_table = $all_date1[0];
		$cols          = $this->get_csv_cols();
		//$summary_table = $this->get_stat_affil();

//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( $data, true ), FILE_APPEND | LOCK_EX );
//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );

		//print_r($test);


		$objPHPExcel = new \PHPExcel();

		$objPHPExcel->getProperties()->setCreator( "Euroroaming" )
		            ->setLastModifiedBy( "Euroroaming" )
		            ->setTitle( "Office 2007 XLSX Test Document" )
		            ->setSubject( "Office 2007 XLSX Test Document" )
		            ->setDescription( "Test document for Office 2007 XLSX, generated using PHP classes." )
		            ->setKeywords( "office 2007 openxml php" )
		            ->setCategory( "Report Euroroaming" );


		/****************************Лист Рефералы**************************************************/

		$objPHPExcel->setActiveSheetIndex( 0 );

		$objPHPExcel->getActiveSheet()->fromArray( $cols, null, 'A1' );
		$objPHPExcel->getActiveSheet()->fromArray( $data, null, 'A2' );


		$objPHPExcel->getActiveSheet()->setAutoFilter( $objPHPExcel->getActiveSheet()->calculateWorksheetDimension() );

		$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setWidth( 20 );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setWidth( 30 );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setWidth( 30 );

		$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'G' )->setAutoSize( true );

		$objPHPExcel->getActiveSheet()->getStyle( 'A1:A' . $objPHPExcel->getActiveSheet()->getHighestRow() )->getAlignment()->setWrapText( true );
		$objPHPExcel->getActiveSheet()->getStyle( 'D1:D' . $objPHPExcel->getActiveSheet()->getHighestRow() )->getAlignment()->setWrapText( true );
		$objPHPExcel->getActiveSheet()->getStyle( 'E1:E' . $objPHPExcel->getActiveSheet()->getHighestRow() )->getAlignment()->setWrapText( true );

		$objPHPExcel->getActiveSheet()->getStyle( 'A1:A500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'B1:B500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'C1:C500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'D1:D500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'E1:E500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'F1:F500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'G1:G500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );

		$objPHPExcel->getActiveSheet()->getStyle( 'A1:G1' )->applyFromArray( array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array( 'rgb' => '4F81BD' )
			)
		) );
		$objPHPExcel->getActiveSheet()->getStyle( "A1:G1" )->getFont()->setBold( true )->getColor()->setRGB( 'FFFFFF' );

		$objPHPExcel->getActiveSheet()->freezePane( 'H2' );

		$objPHPExcel->getActiveSheet()->getStyle( 'F1:F500' )->getNumberFormat()->setFormatCode( '#,##0.00' );

		$objPHPExcel->getActiveSheet()->setTitle( 'Рефералы' );


		/****************************Лист Сводная Таблица**************************************************/

		$objPHPExcel->createSheet();

		$objPHPExcel->setActiveSheetIndex( 1 );

		$summary_table_title = array(
			'Партнер',
			'Email',
			'Сим-карты',
			'Кол-во сим-карт',
			'К выплате',
			'Платежные реквизиты'
		);

		/*******************************
		 * Orange - 58961
		 * Vodafone - 58981
		 * Ortel - 58995
		 * EuropaSim - 59104
		 * Globalsim - 59021
		 * Globalsim Internet - 59004
		 * Globalsim USA - 59135
		 * TravelChat - 59130
		 * Three - 59140
		 * ******************************/
		$sim_cards = array(
			'Orange',
			'Vodafone',
			'Ortel',
			'EuropaSim',
			'Globalsim',
			'Globalsim Internet',
			'Globalsim USA',
			'TravelChat',
			'Three'
		);

		$sim_cards_numbers = array( '58961', '58981', '58995', '59104', '59021', '59004', '59135', '59130', '59140' );

		$objPHPExcel->getActiveSheet()->fromArray( $summary_table_title, null, 'A1' );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setWidth( 35 );


		$i = 2;
		foreach ( $summary_table as $affiliate ) {
			$objPHPExcel->getActiveSheet()->setCellValue( 'A' . $i, $affiliate['campaign'] );
			$objPHPExcel->getActiveSheet()->setCellValue( 'B' . $i, $affiliate['email'] );

			$j = $i;
			$k = 0;
			foreach ( $sim_cards as $sim_card ) {
				$objPHPExcel->getActiveSheet()->setCellValue( 'C' . $j, $sim_card );
				$objPHPExcel->getActiveSheet()->setCellValue( 'D' . $j, $affiliate['simcards_qty'][ $sim_cards_numbers[ $k ] ] );
				$k ++;
				$j ++;
			}

			$objPHPExcel->getActiveSheet()->setCellValue( 'E' . $i, $affiliate['amount'] );
			$objPHPExcel->getActiveSheet()->setCellValue( 'F' . $i, $affiliate['payment_details'] );

			$objPHPExcel->getActiveSheet()->getRowDimension( $i )->setRowHeight( 15 );


			$colo_last_cell = $i + 8;
			$color_block    = 'A' . $i . ':' . 'E' . $colo_last_cell;

			$objPHPExcel->getActiveSheet()
			            ->getStyle( $color_block )
			            ->applyFromArray(
				            array(
					            'fill'    => array(
						            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array( 'rgb' => 'f2f2f2' )
					            ),
					            'borders' => array(
						            'allborders' => array(
							            'style' => PHPExcel_Style_Border::BORDER_THIN,
							            'color' => array( 'rgb' => '000000' )
						            )
					            ),
					            'font'    => array(
						            'color' => array( 'rgb' => '000000' )
					            )
				            )
			            );

			$i += 10;
		}

		$objPHPExcel->getActiveSheet()->getStyle( 'F1:F' . $objPHPExcel->getActiveSheet()->getHighestRow() )->getAlignment()->setWrapText( true );

		$objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setAutoSize( true );
		$objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setAutoSize( true );

		$objPHPExcel->getActiveSheet()->getStyle( 'A1:A500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'B1:B500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'C1:C500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_LEFT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'D1:D500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );
		$objPHPExcel->getActiveSheet()->getStyle( 'E1:E500' )->getAlignment()->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT )->setVertical( PHPExcel_Style_Alignment::VERTICAL_TOP );

		$objPHPExcel->getActiveSheet()->getStyle( 'A1:F1' )->applyFromArray( array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array( 'rgb' => '4F81BD' )
			)
		) );
		$objPHPExcel->getActiveSheet()->getStyle( 'E1' )->applyFromArray( array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array( 'rgb' => '00A100' )
			)
		) );


		$objPHPExcel->getActiveSheet()->getStyle( "A1:F1" )->getFont()->setBold( true )->getColor()->setRGB( 'FFFFFF' );

		$objPHPExcel->getActiveSheet()->getStyle( 'E1:E500' )->getNumberFormat()->setFormatCode( '#,##0.00' );

		$objPHPExcel->getActiveSheet()->freezePane( 'F2' );

		$objPHPExcel->getActiveSheet()->setTitle( 'Сводная таблица' );


// Redirect output to a client’s web browser (Excel2007)
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment; filename=affiliate-wp-export-' . $this->export_type . '-' . date( 'd-m-Y|H:i:s' ) . '.xlsx' );
		header( 'Cache-Control: max-age=0' );
// If you're serving to IE 9, then the following may be needed
		header( 'Cache-Control: max-age=1' );

// If you're serving to IE over SSL, then the following may be needed
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
		$objWriter->save( 'php://output' );
		//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		//$objWriter->save('myfile.xlsx');
		exit;
	}

	/**
	 * Appends data to the export file.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param string $data Optional. Data to append to the export file. Default empty.
	 */
	protected function stash_step_data( $data = '' ) {
		$file  = $this->get_file();
		$file .= $data;

		@file_put_contents( $this->file, $file );
	}

	/**
	 * Calculates and retrieves the offset for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return int Number of items to offset.
	 */
	public function get_offset() {
		return ( $this->step - 1 ) * $this->per_step;
	}

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return int Percentage completed.
	 */
	public function get_percentage_complete() {

		$percentage = 0;

		$current_count = $this->get_current_count();
		$total_count   = $this->get_total_count();

		if ( $total_count > 0 ) {
			$percentage = ( $current_count / $total_count ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	public function finish() {
		affiliate_wp()->utils->data->delete_by_match( "^{$this->batch_id}[0-9a-z\_]+" );
	}

	/**
	 * Retrieves the current, stored count of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @see get_percentage_complete()
	 *
	 * @return int Current number of processed items. Default 0.
	 */
	protected function get_current_count() {
		return affiliate_wp()->utils->data->get( "{$this->batch_id}_current_count", 0 );
	}

	/**
	 * Sets the current count of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param int $count Number of processed items.
	 */
	protected function set_current_count( $count ) {
		affiliate_wp()->utils->data->write( "{$this->batch_id}_current_count", $count );
	}

	/**
	 * Retrieves the total, stored count of items to process.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @see get_percentage_complete()
	 *
	 * @return int Current number of processed items. Default 0.
	 */
	protected function get_total_count() {
		return affiliate_wp()->utils->data->get( "{$this->batch_id}_total_count", 0 );
	}

	/**
	 * Sets the total count of items to process.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param int $count Number of items to process.
	 */
	protected function set_total_count( $count ) {
		affiliate_wp()->utils->data->write( "{$this->batch_id}_total_count", $count );
	}

	/**
	 * Deletes the stored current and total counts of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 */
	protected function delete_counts() {
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_current_count" );
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_total_count" );
	}

}
