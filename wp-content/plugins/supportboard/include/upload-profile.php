<?php
/*
 * ==============
 * Support Board - UPLOAD MODULE PROFILE
 * ==============
 */

session_start();
include("../../../../../wp-load.php");

if (0 < $_FILES['file']['error']) {
    echo 'Error into upload.php file.';
} else {
    $id = $_POST['user_id'];
    if (!file_exists('../../../uploads/supportboard/' . $id)) {
        mkdir('../../../uploads/supportboard/' . $id, 0777, true);
    }
    $infos = pathinfo($_FILES['file']['name']);
    if ($infos['extension'] == "jpg" || $infos['extension'] == "png") {
        $url = "../../../uploads/supportboard/" . $id . "/" . $id;
        move_uploaded_file($_FILES['file']['tmp_name'], $url . "." . $infos['extension']);
        if ($infos['extension'] == "png") {
            imagejpeg(imagecreatefrompng($url . "." . $infos['extension']), $url . ".jpg", 90);
            unlink($url . "." . $infos['extension']);
        }
    }
}
?>
