<?php


$target_dir = "../uploads/";


$file_array = array();


$file_count = count($_FILES['fileToUpload']['name']);
$file_keys = array_keys($_FILES['fileToUpload']);

for ($i=0; $i<$file_count; $i++) {
    foreach ($file_keys as $key) {
        $file_array[$i][$key] = $_FILES['fileToUpload'][$key][$i];
    }
}
echo "<pre>";
var_dump($file_array);
echo "</pre>";

foreach( $file_array as $file)
{

    $target_file = $target_dir . basename($file['name']);

    print_r($target_file);
    $uploadOk = 1;
    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);

    if (isset($_POST["submit"])) {
        $uploadOk = 1;

    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 50000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

//    if ($fileType != "pdf") {
//        echo "Sorry, only pdf files are allowed";
//        $uploadOk = 0;
//    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";

    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "The file " . basename($file["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

}


?>