<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/1/15
 * Time: 6:35 PM
 */
class FormProcessJob {

    public function fire($job, $data)
    {

        $formController = new FormController;
        if(is_array($data))
        {
            $outputAddForms = $formController->addForms($data['uploadedResult']);
            Log::info($outputAddForms);

            $outputProcessForms = $formController->processForms();
            Log::info($outputProcessForms);
        }

        $results = $formController->storeConsolidatedResults($data['feedbackData']);

        $job->delete();


    }

}