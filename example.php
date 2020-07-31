<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

include_once("google-drive-api.class.php");

$strClientId = "97725510481-6c1bjbk54631kc4co96inev3ekqflu0n.apps.googleusercontent.com";
$strClientSecret = "SYH8Cmt-M37-cZPhvLTesLJw";

$strRefreshToken = "1//0dIjOhXnunXX1CgYIARAAGA0SNgF-L9Ir6ZGFhIguXJu-1op5sgvLmS11XLhgIVHfUW7bnAJwQEN6UeF1jY6MooB1JnGuCMhZRw";

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
