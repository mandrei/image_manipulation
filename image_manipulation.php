<?php

/* -----------------
| UPLOAD FORM - validate form and handle submission
----------------- */

if(isset($_POST['upload_form_submitted'])) {

	//error scenario 1
	if(!isset($_FILES['img_upload']) || empty($_FILES['img_upload']['name'])) {

		$error = "Error: You didn't uploaded a file";


	//error scenario 2
	} else if(!isset($_POST['img_name']) || empty($_FILES['img_upload'])) {

		$error = "Error: You didn't specify a file name";

	} else {
		$allowedMIMEs = array("image/jpeg",'image/gif','image/png');

		foreach($allowedMIMEs as $mime) {
			if($mime == $_FILES['img_upload']['type']) {
				$mimeSplitter = explode('/',$mime);
				$fileExt = $mimeSplitter[1];
				$newPath = "imgs/".$_POST['img_name'].'.'.$fileExt;

				break;
			}
		}

		//errror sceanario 3
		if(file_exists($newPath)) {
			$error = "Error: A file with that name already exists";

		//error scenario 4
		} else if(!isset($newPath)) {
			$error = "Error: Invalid file format - please upload a picture file";

		//error scenario 5
		} else if(!copy($_FILES['img_upload']['tmp_name'],$newPath)) {

			$error ="Error: Could not save file to server";

		} else {

			$_SESSION['newPath'] = $newPath;
			$_SESSION['fileExt'] = $fileExt;	
		}
	}
}

/* -----------------
| CROP saved image
----------------- */

if(isset($_GET["crop_attempt"])) {

	switch($_SESSION["fileExt"][1]) {

		case "jpg": case "jpeg":
		$source_img = imagecreatefromjpeg($_SESSION['newPath']);
		$dest_img = imagecreatetruecolor($_GET["crop_w"], $_GET["crop_h"]);
		break;

		case "gif":
		$source_img = imagecreatefromgif($_SESSION['newPath']);
		$dest_img = imagecreate($_GET["crop_w"], $_GET["crop_h"]);
		break;

		case "png":
		$source_img = imagecreatefrompng($_SESSION["newPath"]);
		$dest_img = imagecreate($_GET["crop_w"], $_GET["crop_h"]);
		break;
	}

	 imagecopy($dest_img,$source_img,0,0,$_GET['crop_1'],$_GET["crop_t"],$_GET['crop_w'],$_GET['crop_h']);

	 switch($_SESSION["fileExt"][1]) {

	 	case "jpg": case "jpeg":
	 	imagejpeg($dest_img,$_SESSION["newPath"]); break;

	 	case "gif":
	 	imagegif($dest_img,$_SESSION["newPath"]); break;

	 	case "png";
	 	imagepng($dest_img, $_SESSION["newPath"]); break;
	 }


	 header("Location: index.php");
}