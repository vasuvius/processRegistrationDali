<?php

// grab what's needed to configure dropbox
include('config.php');

// call php modules
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

// create app instance

$app = new DropboxApp($client_id, $client_secret, $access_token);

$dropbox = new Dropbox($app);

//open a file to write to locally
$filename = "registrationMulti.csv";

$filepath = __DIR__ . "/" . $filename;

$subjectID = (string) $_POST['subjectID']; // grab the subject ID

$headsetID = (string) $_POST['headsetID']; // grab headset ID

$makeNew = (string) $_POST['makeNew']; // BOOL: if true, makeNew, if False, don't

$SIDFound = false; // if SID found, no need to append

$file = fopen($filename,"r");
while(! feof($file))
{
	$row = fgetcsv($file);
	if ($row[0] == $headsetID){
		// if the headset is found, check SID
		if ($row[1] == $subjectID){
			$SIDFound = true;
			echo($row[0] . "|"); // if found, print headsetID
		}
		else{
			// all other subect ID's on the headset, up to the SID we care about
			// first request to the server will be a blank request to get all SID's
			echo($row[1] . "|"); // separate by |, can be parsed in unity
		}
	}
}
fclose($file);

if ($makeNew == "true"){
	if ($SIDFound == false){
		//SID not found, add headset and subject ID ONLY if needed to makeNew
		$file = fopen($filename, "a");
		fputcsv($file, array($headsetID, $subjectID));
		echo("created");
		fclose($file);
	}
}
$dropboxFile = new DropboxFile($filepath);

$dropboxPath = "/Projects/oculusGo/Server/" . $filename;

$file = $dropbox->upload($dropboxFile, $dropboxPath, ['mode' => 'overwrite']);
	

?>