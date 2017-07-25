<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Export
{
    /**
     * Our export type. Used for export-type specific filters/actions.
     * @var string
     * @since 1.0
     */
    public $export_type = 'default';

    /**
     * Can we export?
     *
     * @access public
     * @since 1.0
     * @return bool Whether we can export or not
     */
    public function can_export()
    {
        return (bool)current_user_can(apply_filters('affwp_export_capability', 'export_affiliate_data'));
    }

    /**
     * Set the export headers
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function headers()
    {
        ignore_user_abort(true);

        if (!affwp_is_func_disabled('set_time_limit') && !ini_get('safe_mode'))
            set_time_limit(0);

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=affiliate-wp-export-' . $this->export_type . '-' . date('m-d-Y') . '.csv');
        header("Expires: 0");
    }

    /**
     * Set the CSV columns
     *
     * @access public
     * @since 1.0
     * @return array $cols All the columns
     */
    public function csv_cols()
    {
        $cols = array(
            'id' => __('ID', 'affiliate-wp'),
            'date' => __('Date', 'affiliate-wp')
        );
        return $cols;
    }

    /**
     * Retrieve the CSV columns
     *
     * @access public
     * @since 1.0
     * @return array $cols Array of the columns
     */
    public function get_csv_cols()
    {
        $cols = $this->csv_cols();
        return apply_filters('affwp_export_csv_cols_' . $this->export_type, $cols);
    }

    /**
     * Output the CSV columns
     *
     * @access public
     * @since 1.0
     * @uses Affiliate_WP_Export::get_csv_cols()
     * @return void
     */
    public function csv_cols_out()
    {
        $cols = $this->get_csv_cols();
        $i = 1;
        foreach ($cols as $col_id => $column) {
            echo '"' . $column . '"';
            echo $i == count($cols) ? '' : ',';
            $i++;
        }
        echo "\r\n";
    }

    /**
     * Get the data being exported
     *
     * @access public
     * @since 1.0
     * @return array $data Data for Export
     */
    public function get_data()
    {
        // Just a sample data array
        $data = array(
            0 => array(
                'id' => '',
                'data' => date('F j, Y')
            ),
            1 => array(
                'id' => '',
                'data' => date('F j, Y')
            )
        );

        $data = apply_filters('affwp_export_get_data', $data);
        $data = apply_filters('affwp_export_get_data_' . $this->export_type, $data);

        return $data;
    }

    /**
     * Output the CSV rows
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function csv_rows_out()
    {
        $data = $this->get_data();

        $cols = $this->get_csv_cols();

        // Output each row
        foreach ($data as $row) {
            $i = 1;
            foreach ($row as $col_id => $column) {
                // Make sure the column is valid
                if (array_key_exists($col_id, $cols)) {
                    echo '"' . $column . '"';
                    echo $i == count($cols) + 1 ? '' : ',';
                }

                $i++;
            }
            echo "\r\n";
        }
    }

    /**
     * Perform the export
     *
     * @access public
     * @since 1.0
     * @uses Affiliate_WP_Export::can_export()
     * @uses Affiliate_WP_Export::headers()
     * @uses Affiliate_WP_Export::csv_cols_out()
     * @uses Affiliate_WP_Export::csv_rows_out()
     * @return void
     */


    public function export()
    {

        $data = $this->get_data();
        $cols = $this->get_csv_cols();
        $summary_table = $this->get_stat_affil();

        //print_r($test);


        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Euroroaming")
            ->setLastModifiedBy("Euroroaming")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report Euroroaming");


        /****************************Лист Рефералы**************************************************/

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->fromArray($cols, null, 'A1');
        $objPHPExcel->getActiveSheet()->fromArray($data, null, 'A2');


        $objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle('A1:A' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1:D' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1:E' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getStyle('A1:A500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('B1:B500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('C1:C500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('D1:D500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('E1:E500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('F1:F500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('G1:G500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4F81BD'))));
        $objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $objPHPExcel->getActiveSheet()->freezePane('H2');

        $objPHPExcel->getActiveSheet()->getStyle('F1:F500')->getNumberFormat()->setFormatCode('#,##0.00');

        $objPHPExcel->getActiveSheet()->setTitle('Рефералы');


        /****************************Лист Сводная Таблица**************************************************/

        $objPHPExcel->createSheet();

        $objPHPExcel->setActiveSheetIndex(1);

        $summary_table_title = array('Партнер', 'Email', 'Сим-карты', 'Кол-во сим-карт', 'К выплате', 'Платежные реквизиты');

        /*******************************
         * Orange - 18402
         * Vodafone - 18438
         * Ortel - 18446
         * EuropaSim - 28328
         * Globalsim - 18455
         * Globalsim Internet - 18453
         * ******************************/
        $sim_cards = array('Orange', 'Vodafone', 'Ortel', 'EuropaSim', 'Globalsim', 'Globalsim Internet');

        $sim_cards_numbers = array('18402', '18438', '18446', '28328', '18455', '18453');

        $objPHPExcel->getActiveSheet()->fromArray($summary_table_title, null, 'A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);



        $i = 2;
        foreach ($summary_table as $affiliate) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $affiliate['campaign']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $affiliate['email']);

            $j = $i;
            $k = 0;
            foreach ($sim_cards as $sim_card) {
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $sim_card);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $affiliate['simcards_qty'][$sim_cards_numbers[$k]]);
                $k++;
                $j++;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $affiliate['amount']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $affiliate['payment_details']);

            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);


            $colo_last_cell = $i + 5;
            $color_block = 'A'.$i.':'.'E'.$colo_last_cell;

            $objPHPExcel->getActiveSheet()
                ->getStyle($color_block)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'f2f2f2')
                        ),
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        ),
                        'font'  => array(
                            'color' => array('rgb' => '000000')
                        )
                    )
                );

            $i += 7;
        }

        $objPHPExcel->getActiveSheet()->getStyle('F1:F' . $objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->getStyle('A1:A500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('B1:B500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('C1:C500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('D1:D500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('E1:E500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4F81BD'))));
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '00A100'))));


        $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $objPHPExcel->getActiveSheet()->getStyle('E1:E500')->getNumberFormat()->setFormatCode('#,##0.00');

        $objPHPExcel->getActiveSheet()->freezePane('F2');

        $objPHPExcel->getActiveSheet()->setTitle('Сводная таблица');


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=affiliate-wp-export-' . $this->export_type . '-' . date('d-m-Y') . '.xlsx');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}