<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/1/15
 * Time: 7:37 PM
 */


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

// Print it out as JSON
echo json_encode($newArray);

?>