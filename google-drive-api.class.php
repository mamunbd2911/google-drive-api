<?php
/*****************************************************************
Created :  2017-03-16
Author : Mr. Khwanchai Kaewyos (LookHin)
E-mail : khwanchai@gmail.com
Website : https://www.unzeen.com
Facebook : https://www.facebook.com/LookHin
Source Code On Github : https://github.com/LookHin/google-drive-api
*****************************************************************/


class GoogleDriveApi{
  private $strAccessToken;
  private $strRefreshToken;
  private $strClientId;
  private $strClientSecret;
  private $strScriptUrl;

  private $strApiUrl = "https://www.googleapis.com/drive/v3";
  private $strAuthUrl = "https://www.googleapis.com/oauth2/v4";
  private $strUploadUrl = "https://www.googleapis.com/upload/drive/v3";

  public function __construct($strClientId, $strClientSecret){
    $this->strClientId = $strClientId;
    $this->strClientSecret = $strClientSecret;
    $this->strScriptUrl = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['PHP_SELF']}";
  }

  public function getAuthorizetUrl($scope){
    // Authorize URL
    $strAuthScope = urlencode($scope);
    $strRedirectUrl = urlencode($this->strScriptUrl);

    $strAuthorizetUrl = "https://accounts.google.com/o/oauth2/v2/auth?scope={$strAuthScope}&access_type=offline&redirect_uri={$strRedirectUrl}&response_type=code&client_id={$this->strClientId}";

    return $strAuthorizetUrl;
  }

  public function getRefreshToken($code){
    // Refresh Token
    $strRedirectUrl = $this->strScriptUrl;

    $strUrl = $this->strAuthUrl."/token";

    $arrPostData = array();
    $arrPostData['code'] = $code;
    $arrPostData['client_id'] = $this->strClientId;
    $arrPostData['client_secret'] = $this->strClientSecret;
    $arrPostData['redirect_uri'] = $strRedirectUrl;
    $arrPostData['grant_type'] = "authorization_code";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    $jsonData = json_decode($result,true);

    return $jsonData['refresh_token'];
  }

  public function setAccessTokenFromRefreshToken($strRefreshToken){
    // Get New Access Token
    $this->strRefreshToken = $strRefreshToken;

    $arrPostData = array();
    $arrPostData['client_id'] = $this->strClientId;
    $arrPostData['client_secret'] = $this->strClientSecret;
    $arrPostData['refresh_token'] = $this->strRefreshToken;
    $arrPostData['grant_type'] = "refresh_token";

    $strUrl = $this->strAuthUrl."/token";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    $jsonToken = json_decode($result,true);

    $this->strAccessToken = $jsonToken['access_token'];
  }
  
  public function RenameFile($file_id = "", $new_name = ""){
    // Create Link
    $strUrl = $this->strApiUrl."/files/".$file_id."?supportsAllDrives=true"; //&alt=json
    echo $strUrl."\n";

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);
    
    echo "\n-------------------\n";
    print_r($headers);
    echo "\n-------------------\n";

    $arrPostData = array();
    $arrPostData['name'] = $new_name;
    
    //echo json_encode($arrPostData); exit;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    //curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);
    
    
    print_r($result);
    

    $jsonData = json_decode($result,true);

    return $jsonData;


  }

  public function ListFileAndFolder($parentsId="root", $page=""){

    // List File and Folder
    $strSearch = urlencode("'{$parentsId}' in parents and trashed=false");

    //$strUrl = $this->strApiUrl."/files?corpora=user&orderBy=folder,name&pageToken=".$page."&q=".$strSearch;
    $strUrl = $this->strApiUrl."/files?corpora=drive&orderBy=folder,name&includeItemsFromAllDrives=true&supportsAllDrives=true&pageToken=".$page."&driveId=".$parentsId;
    
    // https://content.googleapis.com/drive/v3/files?corpora=drive&driveId=0AMP9jb1-NdxeUk9PVA&
    // includeItemsFromAllDrives=true&supportsAllDrives=true&key=AIzaSyAa8yy0GdcGPHdtD083HiGGx_S0vMPScDM

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    $arrListFile = json_decode($result,true);

    if(empty($arrListFile['nextPageToken'])){
      return $arrListFile['files'];
    }else{
      return array_merge($arrListFile['files'], $this->ListFileAndFolder($parents, $arrListFile['nextPageToken']));
    }
  }

  public function CreateFolder($parentsId="root", $strFolderName){

    // Create Folder
    $strUrl = $this->strApiUrl."/files";

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);

    $arrPostData = array();
    $arrPostData['mimeType'] = "application/vnd.google-apps.folder";
    $arrPostData['name'] = $strFolderName;
    $arrPostData['parents'] = array($parentsId);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    $jsonData = json_decode($result,true);

    return $jsonData['id'];
  }

  public function Delete($strFileId){
    // Delete File and Folder From Id
    $strUrl = $this->strApiUrl."/files/".$strFileId;

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);
  }

  public function Upload($parentsId="root", $file){

    // Create Upload Link
    $strUrl = $this->strUploadUrl."/files?uploadType=resumable";

    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);

    $arrPostData = array();
    $arrPostData['name'] = basename($file);
    $arrPostData['parents'] = array($parentsId);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    preg_match_all("|Location: (.*)\\n|U",$result,$arrUploadLinkUrl, PREG_PATTERN_ORDER);
    $strUploadLinkUrl = $arrUploadLinkUrl[1][0];

    // Upload File
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: OAuth ".urlencode($this->strAccessToken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, trim($strUploadLinkUrl));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result=curl_exec($ch);
    curl_close ($ch);

    $jsonData = json_decode($result,true);

    return $jsonData;
  }


}

?>
