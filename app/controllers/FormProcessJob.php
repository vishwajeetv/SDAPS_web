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
        $value = $data['message']['key'];
        Log::info($value.time());
    }

}