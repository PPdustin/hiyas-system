<?php
// Read the JSON file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonFile = './src/candidates.json';
    $jsonData = file_get_contents($jsonFile);
    $candidates = json_decode($jsonData, true);
    
    // Check if JSON decoding was successful
    if ($candidates === null) {
        die("Error decoding JSON.");
    }
    
    // The ID of the judge to delete
    $targetId = $_POST['id']; // Change this to the ID you want to delete
    
    // Filter out the judge with the matching ID
    $candidates = array_filter($candidates, function ($candidate) use ($targetId) {
        return $candidate['id'] !== $targetId; // Keep only judges that DON'T match the target ID
    });
    
    // Re-index array to maintain order
    $candidates = array_values($candidates);
    
    // Save the updated data back to the JSON file
    file_put_contents($jsonFile, json_encode($candidates, JSON_PRETTY_PRINT));
    
    echo "Judge with ID $targetId has been deleted!";
}
?>
