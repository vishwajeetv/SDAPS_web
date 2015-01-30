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
            Log::info("data started in formProcessJob");
            Log::info($data);
            $outputAddForms = $formController->addForms($data['files']);
            Log::info("add forms output started");
            Log::info($outputAddForms);

            $outputProcessForms = $formController->processForms();
            Log::info("Process forms output started");
            Log::info($outputProcessForms);
        }

        $results = $formController->storeConsolidatedResults($data['feedbackData']);

        $job->delete();


    }

}