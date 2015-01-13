<?php

class FormController extends \BaseController {


	public function postAddForms()
	{

		$fileNames = Input::get("fileNames");

		$fileNamesString = '';
		foreach($fileNames as $fileName)
		{
			$fileNamesString .= ' "'."/var/www/html/uploads/".$fileName.'"';
		}

		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "add" -A "convert" -f '.$fileNamesString;
//		$command = 'date';

		$output = shell_exec( $command );

		return $this->response("success","forms added successfully",$output);
	}

	public function postProcessForms()
	{
		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "recognize"';

		$output = shell_exec( $command );

		$result = $this->generateOutput();

		return $this->response("success","forms added successfully",$result);
	}

	public function generateOutput()
	{
		$command = '/home/ubuntu/Projects/export_csv.sh';

		$output = shell_exec( $command );

// Set your CSV feed
		$feed = '/home/ubuntu/Projects/citizen_feedback/data_5.csv';

// Arrays we'll use later
		$keys = array();
		$newArray = array();

// Function to convert CSV into associative array
		function csvToArray($file, $delimiter) {
			if (($handle = fopen($file, 'r')) !== FALSE) {
				$i = 0;
				while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
					for ($j = 0; $j < count($lineArray); $j++) {
						$arr[$i][$j] = $lineArray[$j];
					}
					$i++;
				}
				fclose($handle);
			}
			return $arr;
		}

// Do it
		$data = csvToArray($feed, ',');

// Set number of elements (minus 1 because we shift off the first row)
		$count = count($data) - 1;

//Use first row for names
		$labels = array_shift($data);

		foreach ($labels as $label) {
			$keys[] = $label;
		}

// Add Ids, just in case we want them later
		$keys[] = 'id';

		for ($i = 0; $i < $count; $i++) {
			$data[$i][] = $i;
		}

// Bring it all together
		for ($j = 0; $j < $count; $j++) {
			$d = array_combine($keys, $data[$j]);
			$newArray[$j] = $d;
		}
		return $newArray;
	}

	public function postUploadForm()
	{

		$targetDir = "../../uploads/";


		$fileArray = array();


		$fileCount = count($_FILES['fileToUpload']['name']);
		$fileKeys = array_keys($_FILES['fileToUpload']);

		for ($i = 0; $i < $fileCount; $i++) {
			foreach ($fileKeys as $key) {
				$fileArray[$i][$key] = $_FILES['fileToUpload'][$key][$i];
			}
		}

		$uploadedResult = array();


		foreach ($fileArray as $file) {

			$targetFile = $targetDir . basename($file['name']);

			$uploadOk = 1;
			$fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

			$uploadMessages = array();

			if (isset($_POST["submit"])) {

				if (file_exists($targetFile)) {
					array_push($uploadMessages, array(
						"errorExist" => "This file already exists."
					));

				}
				if ($file["size"] > 500000000) {

					array_push($uploadMessages, array(
						"errorFileLarge" => "Sorry, this file is too large"
					));
				}

//        if ($fileType != "pdf") {
//            array_push($uploadMessages, array(
//                "errorFileType" =>  "Sorry, only pdf files are allowed."
//            ));
//        }


				if (count($uploadMessages) > 0) {
					array_push($uploadedResult,
						array(
							"fileName" => basename($file["name"]),
							"uploadStatus" => "failure",
							"message" => $uploadMessages
						));

				} else {
					if (move_uploaded_file($file["tmp_name"], $targetFile)) {

						array_push($uploadMessages, array(
							"successUpload" => "The file has been successfully uploaded"
						));
						array_push($uploadedResult,
							array(
								"fileName" => basename($file["name"]),
								"uploadStatus" => "success",
								"message" => $uploadMessages
							));

					} else {

						array_push($uploadMessages, array(
							"successUpload" => "Failed to upload the file"
						));

						array_push($uploadedResult,
							array(
								basename($file["name"]) => "failure",
								"message" => $uploadMessages
							));
					}
				}
			}
		}

		return $this->response("success", "created", $uploadedResult);
	}

}
