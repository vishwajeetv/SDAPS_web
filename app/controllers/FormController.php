<?php
use SoapBox\Formatter\Formatter;

class FormController extends \BaseController {

	public function postStartListener()
	{

		$output = shell_exec("cd /var/www/html/sdaps/ && php artisan queue:listen 2>&1");
		return $this->response("success","queue started",$output);
	}

	public function postQueue()
	{
		$message = array('key' => 'value');

		Queue::push('FormProcessJob', array('message' => $message));

		return $this->response("success","added to queue",$message);
	}
    public function postShow()
    {
//        $data = DB::collection('dept')->get();
////        return View::make('show');
//        return View::make('form.show')->with('data', $data);
        $data = Department::all();

        return $this->response("success","forms added successfully",$data);
    }

	public function addForms($uploadedResults)
	{
		$fileNamesString = '';
		foreach($uploadedResults as $uploadedResult)
		{
			$fileName = $uploadedResult['fileName'];
			$fileNamesString .= ' "'."/var/www/html/uploads/".$fileName.'"';
		}

		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "add" -A "convert" -f '.$fileNamesString;

		$output = shell_exec( $command );

		return $output;
	}

	public function processForms()
	{
		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "recognize"';

		$output1 = shell_exec( $command );

		$command = '/var/www/html/sdaps/scripts/rmcsvs.sh 2>&1';

		$output2 = shell_exec( $command );

		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "csv export" 2>&1';

		$output3 = shell_exec( $command );

		$outputAll = $output1.$output2.$output3;
		return $outputAll;

	}

	public function getPDFPages($document)
	{


		// Parse entire output
		exec("pdfinfo $document", $output);

		// Iterate through lines
		$pageCount = 0;
		foreach($output as $op)
		{
			// Extract the number
			if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1)
			{
				$pageCount = intval($matches[1]);
				break;
			}
		}

		return $pageCount;
	}

	public function postRetrieveReports()
	{

		$processedResult = $this->generateResult();
		$filteredResult = $this->filterResult($processedResult);

		$reports = $this->generateReports($filteredResult);

		return $this->response("success","results retrieval done",$processedResult);
	}

	public function addFeedbackDataToConsolidatedResults($feedbackData)
	{
		$filteredResults = $this->consolidateAllResults();


//		for($i = 0; $i < sizeof($filteredResults); $i++)
//		{
//			$file = array("fileName" => "das");
//			$filteredResults['file'] = $file;
//
//		}
		$results = array();
		$i=0;
		foreach($filteredResults as $result)
		{

			$results[$i]['responses'] = $result;
			$results[$i]['name'] = "";
			$results[$i]['surname'] = "";
			$results[$i]['address'] = "";
			$results[$i]['meeting_reason'] = "";
			$results[$i]['mobile'] = "";
			$results[$i]['email'] = "";
			$i++;
		}
		return $results;

	}

	public function storeConsolidatedResults($feedbackData)
	{
		$results = $this->addFeedbackDataToConsolidatedResults($feedbackData);

		$i=0;
		foreach($results as $result)
		{
			$feedback = Feedback::find($feedbackData[$i]['feedbackId']);
			$feedback->form = $result;
			$feedback->save();
			$i++;
		}

		$feedbacks = Feedback::all();
		return $feedbacks;
	}

	public function postRetrieveConsolidatedResults()
	{
		$feedbacks = Feedback::all();

		return $this->response("success","consolidated results retrieved",$feedbacks);
	}


	public function retrieveGranualData()
	{
		$feedbacks = Feedback::all();

		$granualResults = array();
		foreach($feedbacks as $feedback)
		{

			$responses = $feedback['response']['responses'];

			if (is_array($responses))
			{
				foreach ($responses as $response)
				{
					array_push($granualResults, $response);
				}
			}
		}
		return $granualResults;
	}

	public function postGenerateReportsFromDb()
	{
		//Retrieve granual data from DB
		$granualData = $this->retrieveGranualData();

		//generate reports using it.
		$reports = $this->generateReports($granualData);

		return $this->response("success",'results retrieved',$reports);

	}
	public function consolidateAllResults()
	{


		$processedResult = $this->generateResult();

		$processedConsolidatedResult = $this->generateConsolidatedResult($processedResult);
		$filteredResult = $this->filterConsolidatedResult($processedConsolidatedResult);

		return $filteredResult;

	}

	public function postUploadForm()
	{

		$targetDir = "../../uploads/";

		$feedbackData = array();

		$uploadSuccess = 0;

		$file = $_FILES['file'];

		$uploadedResult = array();


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
			if ($file["size"] > 1000000000) {

				array_push($uploadMessages, array(
					"errorFileLarge" => "Sorry, this file is too large"
				));
			}

			if ($fileType != "pdf") {
				array_push($uploadMessages, array(
					"errorFileType" =>  "Sorry, only pdf files are allowed."
				));
			}


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

					$fileName = $targetFile;

					$totalPages = $this->getPDFPages($fileName);

					array_push($uploadedResult,
						array(
							"fileName" => basename($file["name"]),
							"uploadStatus" => "success",
							"message" => $uploadMessages,
							"totalPages" => $totalPages

						));

					$uploadSuccess = 1;
//						$department = Input::get('department');
					$department = "greatDepartment";


					$feedbackData = $this->createFeedbackRecord($totalPages, $department, $file);

				} else {

					array_push($uploadMessages, array(
						"successUpload" => "Failed to upload the file"
					));

					array_push($uploadedResult,
						array(
							"status" => "failure",
							"message" => $uploadMessages
						));
				}
			}
		}

		if ($uploadSuccess == 1) {
			$jobData = array(
				'feedbackData' => $feedbackData,
				'uploadedResult' => $uploadedResult
			);

			Queue::push('FormProcessJob',
				$jobData
			);
			return $this->response("success", $feedbackData, $uploadedResult);

		} else {
			return $this->response("failed", "failed to upload", $uploadedResult);
		}

	}

	public function postCreateFormRecords()
	{
		$feedbackIds = array();

		for( $i = 0 ; $i < 3 ; $i++ )
		{
			$feedback = new Feedback;

			$feedback->save();

			array_push($feedbackIds, $feedback['_id']);
		}

		return $this->response("success",'created records',$feedbackIds);
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
		$command = '/var/www/html/sdaps/scripts/sdapshell.sh -p "/var/www/html/pmccs_aundh" -a "csv export"';

		$output = shell_exec( $command );

		// Set your CSV feed
		$feed = '/var/www/html/pmccs_aundh/data_1.csv';
//		$feed = '/root/PhpstormProjects/SDAPS_web/data_1.csv';

		/*
		 * Don't touch this code.
		 */
		// Arrays we'll use later
		$keys = array();
		$newArray = array();

		// Function to convert CSV into associative array


		// Do it
		$data = $this->csvToArray($feed, ',');

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

	public function csvToArray($file, $delimiter) {
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

	public function filterConsolidatedResult($processedResults)
	{
		$filteredResult = array();

		$consolidatedFilteredResult = array();


			foreach ($processedResults as $processedResult) {
				$filteredResult = array();
				foreach ($processedResult as $result) {
					if ($result['question'] == 'nothing' || $result['response'] == 'nothing' || $result['response'] == '') {
						continue;
					} else {
						array_push($filteredResult, $result);
					}
				}
				array_push($consolidatedFilteredResult, $filteredResult);
			}

		return $consolidatedFilteredResult;
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

	public function generateConsolidatedResult()
	{
		$resultGenerated = $this->generateOutput();

		$processedResult = array();

		$consolidatedProcessedResult = array();

		foreach ($resultGenerated as $result) {

			$processedResult = array();
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
			array_push($consolidatedProcessedResult, $processedResult);
		}
		return $consolidatedProcessedResult;
	}

	/**
	 * @param $totalPages
	 * @param $department
	 * @param $file
	 * @return mixed
	 */
	public function createFeedbackRecord($totalPages, $department, $file)
	{
		for ($i = 0; $i < $totalPages; $i++) {
			$feedback = new Feedback;

			$feedback->department = $department;

			$feedback->filename = basename($file["name"]);

			$feedback->page = ($i + 1);
			$feedback->save();

			array_push($feedbackData, array('feedbackId' => $feedback['_id'],
				'department' => $department,
				'fileName' => basename($file["name"])
			));
		}
		return $feedbackData;
	}

}