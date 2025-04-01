<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = './src/judges.json';

    // Read existing data
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    } else {
        $data = [];
    }

    // New entry
    $newEntry = [
        'id' => $_POST['id'],
        'name' => $_POST['name'],
        'code' => $_POST['code']
    ];

    // Append new entry
    $data[] = $newEntry;

    $newJson = './src/judges-scores/' . $_POST["name"] . '.json';
    $newData = json_encode([]); // Empty JSON array

    file_put_contents($newJson, $newData);

    // Save updated data
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true, "message" => "Data saved successfully."]);
}
?>