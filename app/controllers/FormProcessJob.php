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
        for( $i = 0; $i < 3; $i++)
        {
            $value = $data['message']['key'];
            Log::info($value.time());

            if($i == 2)
            {
                $job->delete();
            }

        }

    }

}