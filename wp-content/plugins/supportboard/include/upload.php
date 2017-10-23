<?php
/*
 * ==============
 * Support Board - UPLOAD MODULE
 * ==============
 */

session_start();
include("../../../../../../wp-load.php");

$allowed_extensions = array("jpg","jpeg","png","gif","svg","pdf","doc","docx","key","ppt","odt","xls","xlsx","zip","rar","mp3","m4a","ogg","wav","mp4","mov","wmv","avi","mpg","ogv","3gp","3g2","mkv","txt","ico","exe","csv","java","js","xml","unx","ttf","font","css");

if (0 < $_FILES['file']['error']) {
   die("Error into upload.php file.");
} else {
    $file_name = $_FILES['file']['name'];
    $infos = pathinfo($file_name);
    if (in_array($infos['extension'], $allowed_extensions)) {
        $id = $_SESSION['sb-user-infos']['id'];
        if (!file_exists('../../../uploads/supportboard/' . $id)) {
            mkdir('../../../uploads/supportboard/' . $id, 0777, true);
        }
        move_uploaded_file($_FILES['file']['tmp_name'], "../../../uploads/supportboard/" . $id . "/" . $file_name);
    } else {
        die("error extension");
    }
}
?>
