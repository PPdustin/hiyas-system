<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = './src/candidates.json';

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
        'prepageant' => $_POST['prepageant'],
        'number' => $_POST['number']
    ];

    // Append new entry
    $data[] = $newEntry;

    // Save updated data
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true, "message" => "Data saved successfully."]);
}
?>