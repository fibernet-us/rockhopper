<?php

/**
 * ****************************************************************************
 *
 * Uploadicon.php 
 * 
 * Used by showprofile.php to upload and set a usericon.
 * 
 *                               
 */

require_once 'tracking.php';
$path = User::ICON_PATH;

$curUser = doAutoLogin($dbh);
$id = $curUser->getId();

$valid_formats = array("jpg", "png", "gif", "bmp");
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
	$name = $_FILES['photoimg']['name'];
	$size = $_FILES['photoimg']['size'];
	
	if(strlen($name)){
		list($txt, $ext) = explode(".", $name);
		
		if(in_array($ext,$valid_formats)) {
			if($size<(1024*1024)){
							
				$actual_image_name = "user".$id.".".$ext;
				$tmp = $_FILES['photoimg']['tmp_name'];
				$iconurl = $path.$actual_image_name;
			
				if(move_uploaded_file($tmp, $iconurl)){
					
					$curUser->setIconUrl($iconurl);
					echo "<img src='".$iconurl."'  class='preview'>";
				}
				else echo "failed";
			}
			else echo "Image file size max 1 MB";
		}
		else echo "Invalid file format..";
	}
	else echo "Please select image..!";
	exit;
}
?>