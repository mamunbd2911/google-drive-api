<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

include_once("google-drive-api.class.php");

$strClientId = "84411896611-saiou6gsiifo4a3lf1ih4vusba8g6evb.apps.googleusercontent.com";
$strClientSecret = "UevX7gtamo1Q4_K8T8KKrfhG";

$strRefreshToken = "1//0fqeeW-DeuCOVCgYIARAAGA8SNwF-L9Irp_QkBiumWengpEeXFkTR0RmaIl8pmFOZsYyX74FZlK8v2z-YFkUNoGu6gDEztvjOk_I";

// Init Drive Object
$obj = new GoogleDriveApi($strClientId, $strClientSecret);
$obj->setAccessTokenFromRefreshToken($strRefreshToken);

echo "<pre>\n";

$obj->RenameFile("1-BdpXQ-CzprPcJe9jfOlKQrvL5pFILzs", "9999 Rampage 2018 HD 720p [English+Hindi].mkv");

// # List File From Folder Id
//$arrFile = $obj->ListFileAndFolder("0AMP9jb1-NdxeUk9PVA");
//print_r($arrFile);

// # Create Folder In Root Folder
//$obj->CreateFolder("root", "_NEW_FOLDER_NAME_");

// # Create Folder In Parent Folder
//$obj->CreateFolder("_PARENT_FOLDER_ID_", "_NEW_FOLDER_NAME_");

// # Delete File & Folder
//$obj->Delete("_FILE_OR_FOLDER_ID_");

// # Upload File To Root Folder
// $arrResult = $obj->Upload("root", "no-face.png");
// print_r($arrResult);

// # Upload File To Parent Folder
// $arrResult = $obj->Upload("_PARENT_FOLDER_ID_", "no-face.png");
// print_r($arrResult);

echo "</pre>\n";

?>
