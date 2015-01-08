<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/1/15
 * Time: 7:42 PM
 */
$command = '/home/ubuntu/Projects/process_forms.sh';

header('Content-type: application/json');


$output = shell_exec( $command );

echo json_encode($output);

?>