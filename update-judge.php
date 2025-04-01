<?php
// Read the JSON file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $jsonFile = './src/judges.json';
    $jsonData = file_get_contents($jsonFile);
    $dataArray = json_decode($jsonData, true);

    // Check if JSON decoding was successful
    if ($dataArray === null) {
        die("Error decoding JSON.");
    }

    // The ID of the candidate we want to update
    $targetId = $_POST['id']; // Change this to the ID you want to update

    // The new data we want to update
    $updatedData = [
        "name" => $_POST['name'],
        "code" => $_POST['code']
    ];

    // Loop through the candidates array and update the matching id
    foreach ($dataArray as &$judge) {
        if ($judge['id'] == $targetId) {
            $oldName = './src/judges-scores/' . $judge['name'] . '.json';
            $newName = './src/judges-scores/' . $updatedData['name'] . '.json';
            
            if (file_exists($oldName)) {
                rename($oldName, $newName);
            } else {
                echo "File not found: $oldName";
            }

            $judge = array_merge($judge, $updatedData); // Update values
            break; // Stop looping once found
        }
    }

    // Save the updated data back to the JSON file
    file_put_contents($jsonFile, json_encode($dataArray, JSON_PRETTY_PRINT));

    echo "Judge with ID $targetId has been updated!";
}
?>
