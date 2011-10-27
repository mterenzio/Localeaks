<?php

/**
 * Encrypt_Model
 * 
 * @package Encrypt
 */

class Encrypt_Model extends CI_Model
{
	function Encrypt_Text($key, $text)
	{
$gpgSettingsDirectory = "/var/www/.gnupg"; 
$workDirectory = "/var/www/cipherTmp"; 
putenv("GNUPGHOME=$gpgSettingsDirectory");
$prefix = md5(time());
$sTempFile = tempnam($workDirectory, $prefix);
$filename = explode('/var/www/cipherTmp/', $sTempFile);
$sTempEncFile = $sTempFile . ".asc"; 
$nFile = fopen($sTempFile, "w"); 
fwrite($nFile, $text); 
fclose($nFile); 
if( is_file($sTempEncFile) ) 
      unlink($sTempEncFile); 
$sPGPCommand = 
      "/usr/bin/gpg -r $key -ae $sTempFile"; 
system($sPGPCommand);
file_put_contents ('/var/www/releases/'.$filename[1], $text);
if( is_file($sTempFile) ) 
      unlink($sTempFile); 
if( is_file($sTempEncFile) ) 
{ 
     $aEncryptedText = file($sTempEncFile); 
     $sEncryptedText = 
            implode("", $aEncryptedText); 
     //unlink($sTempEncFile);     
} else { 
     $sEncryptedText = 
             "Error, GPG did not create ciphertext."; 
     return $sEncryptedText;
}
return $sTempFile; 
	}
	
	function Encrypt_File($key, $file)
	{
	//remove metadata
	$filename = explode('/var/www/cipherTmp/', $file);
	$pdftkCommand = 
      "/usr/bin/pdftk $file cat output /var/www/cipherTmp/clean-".$filename[1]; 
system($pdftkCommand);	
	$cleanfile = "/var/www/cipherTmp/clean-".$filename[1];
$gpgSettingsDirectory = "/var/www/.gnupg"; 
$workDirectory = "/var/www/cipherTmp"; 
putenv("GNUPGHOME=$gpgSettingsDirectory");
//$sTempFile = tempnam($workDirectory, ""); 
$sTempEncFile = $cleanfile . ".asc"; 
//$nFile = fopen($sTempFile, "w"); 
//fwrite($nFile, $text); 
//fclose($nFile); 
if( is_file($sTempEncFile) ) 
      unlink($sTempEncFile); 
$sPGPCommand = 
      "/usr/bin/gpg -r $key -ae $cleanfile"; 
system($sPGPCommand); 
//if( is_file($sTempFile) ) 
//      unlink($sTempFile); 
if( is_file($sTempEncFile) ) 
{ 
     //$aEncryptedText = file($sTempEncFile); 
     //$sEncryptedText = 
            //implode("", $aEncryptedText); 
     //unlink($sTempEncFile);     
} else { 
     $sTempEncFile = 
             "Error, GPG did not create ciphertext."; 
}
unlink($file);
$movefile = "mv $cleanfile /var/www/releases/";
system($movefile);
//unlink($cleanfile);
return $sTempEncFile; 
	}
	
}
