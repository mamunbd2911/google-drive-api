<?php
/*****************************************************************
Created :  2017-03-16
Author : Mr. Khwanchai Kaewyos (LookHin)
E-mail : khwanchai@gmail.com
Website : https://www.unzeen.com
Facebook : https://www.facebook.com/LookHin
Source Code On Github : https://github.com/LookHin/google-drive-api
*****************************************************************/

include_once("google-drive-api.class.php");

$strClientId = "97725510481-6c1bjbk54631kc4co96inev3ekqflu0n.apps.googleusercontent.com";
$strClientSecret = "SYH8Cmt-M37-cZPhvLTesLJw";

// Init Drive Object
$obj = new GoogleDriveApi($strClientId, $strClientSecret);

if(empty($_GET['code'])){

  $strAuthScope = "https://www.googleapis.com/auth/drive";
  $strAuthorizetUrl = $obj->getAuthorizetUrl($strAuthScope);

  header("Location: {$strAuthorizetUrl}");
  exit;

}else{

  $strRefreshToken = $obj->getRefreshToken($_GET['code']);
  print "Refrest Token = ".$strRefreshToken;

}


?>
