<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/1/15
 * Time: 8:02 PM
 */



$command = '/home/ubuntu/Projects/add_forms.sh';




header('Content-type: application/json');

$output = shell_exec( $command );

echo json_encode($output);

?>