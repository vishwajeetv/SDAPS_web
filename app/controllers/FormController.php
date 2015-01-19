<?php
use SoapBox\Formatter\Formatter;

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

		$output = shell_exec( $command );

		return $this->response("success","forms added successfully",$output);
	}

	public function postProcessForms()
	{
		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "recognize"';

		$output1 = shell_exec( $command );

		$command = '/var/www/html/sdaps/scripts/rmcsvs.sh 2>&1';

		$output2 = shell_exec( $command );

		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "csv export" 2>&1';

		$output3 = shell_exec( $command );

		return $this->response("success","processing done",$output1.$output2.$output3);


	}

	public function postRetrieveReports()
	{
		$processedResult = $this->generateResult();


		$filteredResult = $this->filterResult($processedResult);

		$reports = $this->generateReports($filteredResult);

		return $this->response("success","results retrieval done",$reports);
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

	/**
	 * @param $keySeparated
	 * @return string
	 */
	public function generateGrade($keySeparated)
	{
		switch ($keySeparated[3]) {
			case "0":
				$response = 'excellent';
				break;
			case "1":
				$response = 'good';
				break;
			case "2":
				$response = 'satisfactory';
				break;
			case "3":
				$response = 'unsatisfactory';
				break;
			case "4":
				$response = 'mediocre';
				break;
			default:
				$response = 'nothing';
				break;
		}
		return $response;
	}


	public function generateGradesReport($response, $gradesReportArray)
	{

		switch ($response) {
			case "excellent":
				$gradesReportArray['excellent']++;
				break;
			case "good":
				$gradesReportArray['good']++;
				break;
			case "satisfactory":
				$gradesReportArray['satisfactory']++;
				break;
			case "unsatisfactory":
				$gradesReportArray['unsatisfactory']++;
				break;
			case "mediocre":
				$gradesReportArray['mediocre']++;
				break;
			case "times_1_3":
				$gradesReportArray['times_1_3']++;
				break;
			case "times_3_6":
				$gradesReportArray['times_3_6']++;
				break;
			case "times_6_more":
				$gradesReportArray['times_6_more']++;
				break;
			default:
				$gradesReportArray['nothing'] = 'nothing';
				break;
		}
		return $gradesReportArray;
	}

//	public function generateAppearancesReport($response, $gradesReportArray)
//	{
//		switch($response){
//			case "times_1_3":
//				$gradesReportArray['times_1_3']++;
//				break;
//			case "3-6_times":
//				$gradesReportArray['3-6_times']++;
//				break;
//			case "times_6_more":
//				$gradesReportArray['times_6_more']++;
//				break;
//			default:
//				$gradesReportArray['nothing'] = 'nothing';
//				break;
//		}
//	}

	/**
	 * @param $filteredResult
	 * @return mixed
	 */
	public function generateReports($filteredResult)
	{
		$report = array();

		$gradesReportArray = array();

		$gradesReportArray['excellent'] = $gradesReportArray['good'] =
		$gradesReportArray['satisfactory'] = $gradesReportArray['unsatisfactory'] =
		$gradesReportArray['mediocre'] =  $gradesReportArray['times_1_3'] = $gradesReportArray['times_3_6'] =
		$gradesReportArray['times_6_more'] = 0;


		$report['passage']['count'] = 0;$report['passage']['gradeCount'] = $gradesReportArray;
		$report['stairs']['count'] = 0;$report['stairs']['gradeCount'] = $gradesReportArray;
		$report['water']['count'] = 0;$report['water']['gradeCount'] = $gradesReportArray;
		$report['toilet']['count'] = 0;$report['toilet']['gradeCount'] = $gradesReportArray;
		$report['parking']['count'] = 0;$report['parking']['gradeCount'] = $gradesReportArray;
		$report['waitingTime']['count'] = 0;$report['waitingTime']['gradeCount'] = $gradesReportArray;
		$report['behaviour']['count'] = 0;$report['behaviour']['gradeCount'] = $gradesReportArray;
		$report['attitude']['count'] = 0;$report['attitude']['gradeCount'] = $gradesReportArray;
		$report['actions']['count'] = 0;$report['actions']['gradeCount'] = $gradesReportArray;
		$report['response']['count'] = 0;$report['response']['gradeCount'] = $gradesReportArray;
		$report['satisfaction']['count'] = 0;$report['satisfaction']['gradeCount'] = $gradesReportArray;
		$report['number_of_appearances']['count'] = 0;$report['number_of_appearances']['gradeCount'] = $gradesReportArray;
		$report['total']['count'] = 0;$report['total']['gradeCount'] = $gradesReportArray;


		foreach ($filteredResult as $result) {

//			print_r($result);

			$report['total']['count']++;

			$report['total']['gradeCount'] = $this->generateGradesReport($result['response'], $report['total']['gradeCount']);

			switch ($result['question']) {
				case "passage":
					$report['passage']['count']++;
					$report['passage']['gradeCount'] = $this->generateGradesReport($result['response'], $report['passage']['gradeCount']);
					break;
				case "stairs":
					$report['stairs']['count']++;
					$report['stairs']['gradeCount'] = $this->generateGradesReport($result['response'], $report['stairs']['gradeCount']);
					break;
				case "toilet":
					$report['toilet']['count']++;

					$report['toilet']['gradeCount'] = $this->generateGradesReport($result['response'], $report['toilet']['gradeCount']);
					break;
				case "water":
					$report['water']['count']++;
					$report['water']['gradeCount'] = $this->generateGradesReport($result['response'], $report['water']['gradeCount']);
					break;
				case "parking":
					$report['parking']['count']++;
					$report['parking']['gradeCount'] = $this->generateGradesReport($result['response'], $report['parking']['gradeCount']);
					break;
				case "waiting_time":
					$report['waitingTime']['count']++;
					$report['waitingTime']['gradeCount'] = $this->generateGradesReport($result['response'], $report['waitingTime']['gradeCount']);
					break;
				case "behaviour":
					$report['behaviour']['count']++;
					$report['behaviour']['gradeCount'] = $this->generateGradesReport($result['response'], $report['behaviour']['gradeCount']);
					break;
				case "attitude":
					$report['attitude']['count']++;
					$report['attitude']['gradeCount'] = $this->generateGradesReport($result['response'], $report['attitude']['gradeCount']);
					break;
				case "actions":
					$report['actions']['count']++;
					$report['actions']['gradeCount'] = $this->generateGradesReport($result['response'], $report['actions']['gradeCount']);
					break;
				case "response":
					$report['response']['count']++;
					$report['response']['gradeCount'] = $this->generateGradesReport($result['response'], $report['response']['gradeCount']);
					break;
				case "satisfaction":
					$report['satisfaction']['count']++;
					$report['satisfaction']['gradeCount'] = $this->generateGradesReport($result['response'], $report['satisfaction']['gradeCount']);
					break;
				case "number_of_appearances":
					$report['number_of_appearances']['count']++;
					$report['number_of_appearances']['gradeCount'] = $this->generateGradesReport($result['response'], $report['number_of_appearances']['gradeCount']);
					break;
				default:

			}

		}

		return $report;
	}

	public function generateOutput()
	{
//		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "csv export"';

//		$output = shell_exec( $command );

		// Set your CSV feed
		$feed = '/var/www/html/pmccs_aundh/data_1.csv';

		/*
		 * Don't touch this code.
		 */
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

		$resultGenerated = $newArray;

		return $resultGenerated;
	}

	/**
	 * @param $processedResult
	 * @return mixed
	 */
	public function filterResult($processedResult)
	{
		$filteredResult = array();

		foreach ($processedResult as $result) {
			if ($result['question'] == 'nothing' || $result['response'] == 'nothing' || $result['response'] == '') {
				continue;
			} else {
				array_push($filteredResult, $result);
			}
		}
		return $filteredResult;
	}

	/**
	 * @return array
	 */
	public function generateResult()
	{
		$resultGenerated = $this->generateOutput();

		$processedResult = array();

		foreach ($resultGenerated as $result) {


			foreach ($result as $key => $value) {
				$response = '';
				$question = '';

				$keySeparated = explode('_', $key);

				if (!isset($keySeparated[0])) {
					$keySeparated[0] = 99;
				}

				if (!isset($keySeparated[1])) {
					$keySeparated[1] = 99;
				}

				if (!isset($keySeparated[2])) {
					$keySeparated[2] = 99;
				}
				if (!isset($keySeparated[3])) {
					$keySeparated[3] = 99;
				}

				if ($keySeparated[0] == '1' && $keySeparated[1] == '2') {
					$question = "gender";
					if ($value == '1') {
						switch ($key) {
							case "1_2_0":
								$response = "female";
								break;
							case "1_2_1":
								$response = "male";
								break;
							case "1_2_2":
								$response = "else";
								break;
							case "1_2_3":
								$response = "else";
								break;
							default:
								$response = "nothing";
						}
					}
				} else if ($keySeparated[0] == '3' && $keySeparated[1] == '1') {
					$question = 'number_of_appearances';
					if ($value == '1') {
						switch ($key) {
							case "3_1_0":
								$response = "times_1_3";
								break;
							case "3_1_1":
								$response = "times_3_6";
								break;
							case "3_1_2":
								$response = "times_6_more";
								break;
							default:
								$response = "nothing";
						}

					}
				}

				if ($keySeparated[0] == '2') {
					$response = $this->generateGrade($keySeparated);
					if ($value == '1') {
						switch ($keySeparated[0] . '_' . $keySeparated[1] . '_' . $keySeparated[2]) {
							case "2_1_1":
								$question = "passage";

								break;
							case "2_1_2":
								$question = "stairs";
								break;
							case "2_1_3":
								$question = "toilet";
								break;
							case "2_1_4":
								$question = "water";
								break;
							case "2_1_5":
								$question = "parking";
								break;
							case "2_2_1":
								$question = "waiting_time";
								break;
							case "2_2_2":
								$question = "behaviour";
								break;
							case "2_2_3":
								$question = "attitude";
								break;
							case "2_2_4":
								$question = "actions";
								break;
							case "2_2_5":
								$question = "response";
								break;
							case "2_2_6":
								$question = "satisfaction";
								break;
							default:
								$question = "nothing";
						}
					} else {
						$response = 'nothing';
					}
				}

				array_push($processedResult,
					array(

						"question" => $question,
						"response" => $response
					)
				);
			}
		}
		return $processedResult;
	}


}