<?php


$targetDir = "../uploads/";


$fileArray = array();


$fileCount = count($_FILES['fileToUpload']['name']);
$fileKeys = array_keys($_FILES['fileToUpload']);

for ($i=0; $i<$fileCount; $i++) {
    foreach ($fileKeys as $key) {
        $fileArray[$i][$key] = $_FILES['fileToUpload'][$key][$i];
    }
}

foreach( $fileArray as $file)
{

    $targetFile = $targetDir . basename($file['name']);

    $uploadOk = 1;
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

    $uploadMessages = array();

    if (isset($_POST["submit"])) {

        if (file_exists($targetFile)) {
            array_push($uploadMessages, array(
               "errorExist" =>  "This file already exists."
            ));

        }
        if ($file["size"] > 500000000) {

            array_push($uploadMessages, array(
                "errorFileLarge" =>  "Sorry, this file is too large"
            ));
         }

//        if ($fileType != "pdf") {
//            array_push($uploadMessages, array(
//                "errorFileType" =>  "Sorry, only pdf files are allowed."
//            ));
//        }

    $uploadedResult = array();

    if (count($uploadMessages) > 0) {
        array_push($uploadedResult,
            array(
                basename($file["name"]) => "failure",
                "message" => $uploadMessages
            ));

    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {

            array_push($uploadMessages, array(
                "successUpload" =>  "The file has been successfully uploaded"
            ));
            array_push($uploadedResult,
            array(
                basename($file["name"]) => "success",
                "message" => $uploadMessages
            ));

        } else {

            array_push($uploadMessages, array(
                "successUpload" =>  "Failed to upload the file"
            ));

            array_push($uploadedResult,
                array(
                    basename($file["name"]) => "failure",
                    "message" => $uploadMessages
                ));
        }
    }
}


    json_encode($uploadedResult);

//    header('Content-type: application/json');

    print_r( $uploadedResult);
}


?>