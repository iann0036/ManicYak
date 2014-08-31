<?php
include_once "templates/base.php";
session_start();

set_include_path("../src/" . PATH_SEPARATOR . get_include_path());
require_once 'Google/Client.php';
require_once 'Google/Http/MediaFileUpload.php';
require_once 'Google/Service/Drive.php';


/************************************************
  We'll setup an empty 20MB file to upload.
 ************************************************/
DEFINE("TESTFILE", 'testfile.txt');
if (!file_exists(TESTFILE)) {
  $fh = fopen(TESTFILE, 'w');
  fseek($fh, 1024*1024*20);
  fwrite($fh, "!", 1);
  fclose($fh);
}

$client_id = '660857930175-8elhqa0qmss6hmf45d72su2o9mv4jqhp.apps.googleusercontent.com';
$client_secret = 'g_DGtr9ZL4fwoeEI92BwDQWt';
$redirect_uri = 'http://dev.ian.mn/google-api-php-client-master/examples/fileupload.php';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$service = new Google_Service_Drive($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['upload_token ']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['upload_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['upload_token']) && $_SESSION['upload_token']) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

/************************************************
  If we're signed in then lets try to upload our
  file.
 ************************************************/
if ($client->getAccessToken()) {
	$file = new Google_Service_Drive_DriveFile();

	//Setup the Folder to Create
	$file->setTitle('Project Folder');
	$file->setDescription('A Project Folder');
	$file->setMimeType('application/vnd.google-apps.folder');

	$createdFile = $service->files->insert($file, array(
		'mimeType' => 'application/vnd.google-apps.folder',
	));
	
	var_dump($createdFile->id);

  $file = new Google_Service_Drive_DriveFile();
  $file->title = "Big File.avi";
  $chunkSizeBytes = 1 * 1024 * 1024;
  
  $parent = new Google_Service_Drive_ParentReference();
  $parent->setId($createdFile->id);
  $file->setParents(array($parent));

  // Call the API with the media upload, defer so it doesn't immediately return.
  $client->setDefer(true);
  $request = $service->files->insert($file);

  // Create a media file upload to represent our upload process.
  $media = new Google_Http_MediaFileUpload(
      $client,
      $request,
      'video/avi',
      null,
      true,
      $chunkSizeBytes
  );
  $media->setFileSize(filesize(TESTFILE));

  // Upload the various chunks. $status will be false until the process is
  // complete.
  $status = false;
  $handle = fopen(TESTFILE, "rb");
  while (!$status && !feof($handle)) {
    $chunk = fread($handle, $chunkSizeBytes);
    $status = $media->nextChunk($chunk);
  }

  // The final value of $status will be the data from the API for the object
  // that has been uploaded.
  $result = false;
  if ($status != false) {
    $result = $status;
  }

  fclose($handle);
}

echo $authUrl;