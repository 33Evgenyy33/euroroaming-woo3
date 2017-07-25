<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <title>Empty Template</title>

</head>
<body
    style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #fafafa">

<style id="media-query">
    /* Client-specific Styles & Reset */
    #outlook a {
        padding: 0;
    }

    /* .ExternalClass applies to Outlook.com (the artist formerly known as Hotmail) */
    .ExternalClass {
        width: 100%;
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
        line-height: 100%;
    }

    #backgroundTable {
        margin: 0;
        padding: 0;
        width: 100% !important;
        line-height: 100% !important;
    }

    /* Buttons */
    .button a {
        display: inline-block;
        text-decoration: none;
        -webkit-text-size-adjust: none;
        text-align: center;
    }

    .button a div {
        text-align: center !important;
    }

    /* Outlook First */
    body.outlook p {
        display: inline !important;
    }

    /*  Media Queries */
    @media only screen and (max-width: 600px) {
        table[class="body"] img {
            height: auto !important;
            width: 100% !important;
        }

        table[class="body"] img.fullwidth {
            max-width: 100% !important;
        }

        table[class="body"] center {
            min-width: 0 !important;
        }

        table[class="body"] .container {
            width: 95% !important;
        }

        table[class="body"] .row {
            width: 100% !important;
            display: block !important;
        }

        table[class="body"] .wrapper {
            display: block !important;
            padding-right: 0 !important;
        }

        table[class="body"] .columns, table[class="body"] .column {
            table-layout: fixed !important;
            float: none !important;
            width: 100% !important;
            padding-right: 0px !important;
            padding-left: 0px !important;
            display: block !important;
        }

        table[class="body"] .wrapper.first .columns, table[class="body"] .wrapper.first .column {
            display: table !important;
        }

        table[class="body"] table.columns td, table[class="body"] table.column td, .col {
            width: 100% !important;
        }

        table[class="body"] table.columns td.expander {
            width: 1px !important;
        }

        table[class="body"] .right-text-pad, table[class="body"] .text-pad-right {
            padding-left: 10px !important;
        }

        table[class="body"] .left-text-pad, table[class="body"] .text-pad-left {
            padding-right: 10px !important;
        }

        table[class="body"] .hide-for-small, table[class="body"] .show-for-desktop {
            display: none !important;
        }

        table[class="body"] .show-for-small, table[class="body"] .hide-for-desktop {
            display: inherit !important;
        }

        .mixed-two-up .col {
            width: 100% !important;
        }
    }

    @media screen and (max-width: 600px) {
        div[class="col"] {
            width: 100% !important;
        }
    }

    @media screen and (min-width: 601px) {
        table[class="container"] {
            width: 600px !important;
        }
    }
</style>
<table class="body"
       style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 100%;width: 100%;table-layout: fixed"
       cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
    <tr style="vertical-align: top">
        <td class="center"
            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;text-align: center;background-color: #fafafa"
            align="center" valign="top">

            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: transparent"
                   cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                        width="100%">
                        <!--[if gte mso 9]>
                        <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                        <![endif]-->
                        <!--[if (IE)]>
                        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                        <![endif]-->
                        <table class="container"
                               style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                               cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                            <tbody>
                            <tr style="vertical-align: top">
                                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                    width="100%">
                                    <table class="block-grid"
                                           style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #000000;background-color: transparent"
                                           cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                        <tbody>
                                        <tr style="vertical-align: top">
                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                <!--[if (gte mso 9)|(IE)]>
                                                <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                                       cellspacing="0" border="0">
                                                    <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                                <td valign="top" width="600"><![endif]-->
                                                <div class="col num12"
                                                     style="display: inline-block;vertical-align: top;width: 100%">
                                                    <table
                                                        style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                        cellpadding="0" cellspacing="0" align="center" width="100%"
                                                        border="0">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    align="center" width="100%" border="0"
                                                                    cellpadding="0" cellspacing="0">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px"
                                                                            align="center">
                                                                            <div style="height: 1px;">
                                                                                <table
                                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 1px solid #BBBBBB;width: 100%"
                                                                                    align="center" border="0"
                                                                                    cellspacing="0">
                                                                                    <tbody>
                                                                                    <tr style="vertical-align: top">
                                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                                                                            align="center"></td>
                                                                                    </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    cellpadding="0" cellspacing="0" width="100%"
                                                                    border="0">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px"
                                                                            align="center">
                                                                            <div align="center">

                                                                                <img class="center"
                                                                                     style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 400px;max-width: 400px"
                                                                                     align="center" border="0"
                                                                                     src="https://euroroaming.ru/wp-content/uploads/2016/03/Logosvg-new.png"
                                                                                     alt="Image" title="Image"
                                                                                     width="400">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    align="center" width="100%" border="0"
                                                                    cellpadding="0" cellspacing="0">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px"
                                                                            align="center">
                                                                            <div style="height: 1px;">
                                                                                <table
                                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 1px solid #BBBBBB;width: 100%"
                                                                                    align="center" border="0"
                                                                                    cellspacing="0">
                                                                                    <tbody>
                                                                                    <tr style="vertical-align: top">
                                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                                                                            align="center"></td>
                                                                                    </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                                <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--[if mso]>
                        </td></tr></table>
                        <![endif]-->
                        <!--[if (IE)]>
                        </td></tr></table>
                        <![endif]-->
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: #1e73be"
                   cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                        width="100%">
                        <!--[if gte mso 9]>
                        <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                        <![endif]-->
                        <!--[if (IE)]>
                        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                        <![endif]-->
                        <table class="container"
                               style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                               cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                            <tbody>
                            <tr style="vertical-align: top">
                                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                    width="100%">
                                    <table class="block-grid"
                                           style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #000000;background-color: transparent"
                                           cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                        <tbody>
                                        <tr style="vertical-align: top">
                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                <!--[if (gte mso 9)|(IE)]>
                                                <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                                       cellspacing="0" border="0">
                                                    <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                                <td valign="top" width="600"><![endif]-->
                                                <div class="col num12"
                                                     style="display: inline-block;vertical-align: top;width: 100%">
                                                    <table
                                                        style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                        cellpadding="0" cellspacing="0" align="center" width="100%"
                                                        border="0">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
                                                                            <div
                                                                                style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                                                                <div
                                                                                    style="font-size:14px;line-height:17px;text-align:center;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                                                                    <h1 style="word-break: normal;font-size: 14px;line-height: 17px;text-align: center">
                                                                                        <span
                                                                                            style="font-size: 26px; line-height: 31px; color: rgb(255, 255, 255);"><?php echo $email_heading; ?></span>
                                                                                    </h1></div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                                <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--[if mso]>
                        </td></tr></table>
                        <![endif]-->
                        <!--[if (IE)]>
                        </td></tr></table>
                        <![endif]-->
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: transparent"
                   cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                        width="100%">
                        <!--[if gte mso 9]>
                        <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                        <![endif]-->
                        <!--[if (IE)]>
                        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                        <![endif]-->
                        <table class="container"
                               style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                               cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                            <tbody>
                            <tr style="vertical-align: top">
                                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                    width="100%">
                                    <table class="block-grid two-up"
                                           style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #333;background-color: transparent"
                                           cellpadding="0" cellspacing="0" width="100%" bgcolor="transparent">
                                        <tbody>
                                        <tr style="vertical-align: top">
                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                                <!--[if (gte mso 9)|(IE)]>
                                                <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                                       cellspacing="0" border="0">
                                                    <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                                <td valign="top" width="300"><![endif]-->
                                                <div class="col num6"
                                                     style="display: inline-block;vertical-align: top;text-align: center;width: 300px">
                                                    <table
                                                        style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                        cellpadding="0" cellspacing="0" align="center" width="100%"
                                                        border="0">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px dashed #BBBBBB;border-right: 0px dashed #BBBBBB;border-bottom: 0px dashed #BBBBBB;border-left: 0px dashed #BBBBBB">
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    width="100%" border="0" cellspacing="0"
                                                                    cellpadding="0">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td class="button-container"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px"
                                                                            align="center">
                                                                            <table
                                                                                style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                                width="100%" border="0"
                                                                                cellspacing="0" cellpadding="0"
                                                                                align="center">
                                                                                <tbody>
                                                                                <tr style="vertical-align: top">
                                                                                    <td class="button"
                                                                                        style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                                                                        width="100%" align="center"
                                                                                        valign="middle">
                                                                                        <!--[if mso]>
                                                                                        <v:roundrect
                                                                                            xmlns:v="urn:schemas-microsoft-com:vml"
                                                                                            xmlns:w="urn:schemas-microsoft-com:office:word"
                                                                                            href="https://euroroaming.ru"
                                                                                            style="
                    height:42px;
                    v-text-anchor:middle;
                    width:298px;"
                                                                                            arcsize="10%"
                                                                                            strokecolor="#17AED5"
                                                                                            fillcolor="#17AED5">
                                                                                            <w:anchorlock/>
                                                                                            <center
                                                                                                style="color:#ffffff;
                      font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;
                      font-size:16px;">
                                                                                        <![endif]-->
                                                                                        <!--[if !mso]><!- - -->
                                                                                        <div style="display: inline-block;
              border-radius: 4px;
              -webkit-border-radius: 4px;
              -moz-border-radius: 4px;
              max-width: 100%;
              width: 100%;
              border-top: 0px solid transparent;
              border-right: 0px solid transparent;
              border-bottom: 0px solid transparent;
              border-left: 0px solid transparent;" align="center">

                                                                                            <table
                                                                                                style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 42"
                                                                                                width="100%"
                                                                                                border="0"
                                                                                                cellspacing="0"
                                                                                                cellpadding="0">
                                                                                                <tbody>
                                                                                                <tr style="vertical-align: top">
                                                                                                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                   -webkit-border-radius: 4px;                   -moz-border-radius: 4px;                  color: #ffffff;                  background-color: #17AED5;                  padding-top: 5px;                   padding-right: 20px;                  padding-bottom: 5px;                  padding-left: 20px;                  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align: center"
                                                                                                        valign="middle">
                                                                                                        <!--<![endif]-->
                                                                                                        <a style="display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #17AED5;color: #ffffff"
                                                                                                           href="https://euroroaming.ru"
                                                                                                           target="_blank">
                                                                                                            <span
                                                                                                                style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;line-height:32px;">Главная Евророуминг</span>
                                                                                                        </a>
                                                                                                        <!--[if !mso]><!- - -->
                                                                                                    </td>
                                                                                                </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div><!--<![endif]-->
                                                                                        <!--[if mso]>
                                                                                        </center>
                                                                                        </v:roundrect>
                                                                                        <![endif]-->
                                                                                    </td>
                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><![endif]--><!--[if (gte mso 9)|(IE)]>
                                                <td valign="top" width="300"><![endif]-->
                                                <div class="col num6"
                                                     style="display: inline-block;vertical-align: top;text-align: center;width: 300px">
                                                    <table
                                                        style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                        cellpadding="0" cellspacing="0" align="center" width="100%"
                                                        border="0">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 0px dashed #BBBBBB;border-bottom: 0px solid transparent;border-left: 0px solid transparent">
                                                                <table
                                                                    style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                    width="100%" border="0" cellspacing="0"
                                                                    cellpadding="0">
                                                                    <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td class="button-container"
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px"
                                                                            align="center">
                                                                            <table
                                                                                style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                                                width="100%" border="0"
                                                                                cellspacing="0" cellpadding="0"
                                                                                align="center">
                                                                                <tbody>
                                                                                <tr style="vertical-align: top">
                                                                                    <td class="button"
                                                                                        style="word-break: break-word;border-collapse: collapse !important;vertical-align: top"
                                                                                        width="100%" align="center"
                                                                                        valign="middle">
                                                                                        <!--[if mso]>
                                                                                        <v:roundrect
                                                                                            xmlns:v="urn:schemas-microsoft-com:vml"
                                                                                            xmlns:w="urn:schemas-microsoft-com:office:word"
                                                                                            href="https://euroroaming.ru/shop/"
                                                                                            style="
                    height:42px;
                    v-text-anchor:middle;
                    width:298px;"
                                                                                            arcsize="10%"
                                                                                            strokecolor="#17AED5"
                                                                                            fillcolor="#17AED5">
                                                                                            <w:anchorlock/>
                                                                                            <center
                                                                                                style="color:#ffffff;
                      font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;
                      font-size:16px;">
                                                                                        <![endif]-->
                                                                                        <!--[if !mso]><!- - -->
                                                                                        <div style="display: inline-block;
              border-radius: 4px;
              -webkit-border-radius: 4px;
              -moz-border-radius: 4px;
              max-width: 100%;
              width: 100%;
              border-top: 0px solid transparent;
              border-right: 0px solid transparent;
              border-bottom: 0px solid transparent;
              border-left: 0px solid transparent;" align="center">

                                                                                            <table
                                                                                                style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 42"
                                                                                                width="100%"
                                                                                                border="0"
                                                                                                cellspacing="0"
                                                                                                cellpadding="0">
                                                                                                <tbody>
                                                                                                <tr style="vertical-align: top">
                                                                                                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;border-radius: 4px;                   -webkit-border-radius: 4px;                   -moz-border-radius: 4px;                  color: #ffffff;                  background-color: #17AED5;                  padding-top: 5px;                   padding-right: 20px;                  padding-bottom: 5px;                  padding-left: 20px;                  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align: center"
                                                                                                        valign="middle">
                                                                                                        <!--<![endif]-->
                                                                                                        <a style="display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #17AED5;color: #ffffff"
                                                                                                           href="https://euroroaming.ru/instructions/"
                                                                                                           target="_blank">
                                                                                                            <span
                                                                                                                style="font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;line-height:32px;">Инструкции</span>
                                                                                                        </a>
                                                                                                        <!--[if !mso]><!- - -->
                                                                                                    </td>
                                                                                                </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div><!--<![endif]-->
                                                                                        <!--[if mso]>
                                                                                        </center>
                                                                                        </v:roundrect>
                                                                                        <![endif]-->
                                                                                    </td>
                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                                <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--[if mso]>
                        </td></tr></table>
                        <![endif]-->
                        <!--[if (IE)]>
                        </td></tr></table>
                        <![endif]-->
                    </td>
                </tr>
                </tbody>
            </table>
