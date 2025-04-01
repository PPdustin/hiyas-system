<?php
    $jsonFile = './src/judges.json';
    $jsonData = file_get_contents($jsonFile);
    $dataArray = json_decode($jsonData, true);

    // Check if JSON decoding was successful
    if ($dataArray === null) {
        die("Error decoding JSON.");
    }

    // The ID of the candidate we want to update
    $targetCode = $_GET['code'] ?? ""; // Change this to the ID you want to update

    // The new data we want to update


    $judgeName = '';
    $isAuthorized = false;
    // Loop through the candidates array and update the matching id
    foreach ($dataArray as &$judge) {
        if ($judge['code'] == $targetCode) {
            $judgeName = $judge['name']; // Update values
            $isAuthorized = true;
            break; // Stop looping once found
        }
    }



    if (!$isAuthorized) {
?>
    <h1>YOU ARE NOT AUTHORIZED</h1>
    <?php
    exit; // STOP execution here if unauthorized
}
?>