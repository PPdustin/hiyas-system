<?php
// Read the JSON file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonFile = './src/judges.json';
    $jsonData = file_get_contents($jsonFile);
    $judges = json_decode($jsonData, true);

    // Check if JSON decoding was successful
    if ($judges === null) {
        die("Error decoding JSON.");
    }

    // The ID of the judge to delete
    $targetId = $_POST['id']; // Change this to the ID you want to delete
    $judgeToDelete = null;

    // Find the judge before filtering (to get their name for file deletion)
    foreach ($judges as $judge) {
        if ($judge['id'] == $targetId) {
            $judgeToDelete = $judge;
            break;
        }
    }

    // Filter out the judge with the matching ID
    $judges = array_filter($judges, function ($judge) use ($targetId) {
        return $judge['id'] !== $targetId; // Keep only judges that DON'T match the target ID
    });

    // Re-index array to maintain order
    $judges = array_values($judges);

    // Save the updated data back to the JSON file
    file_put_contents($jsonFile, json_encode($judges, JSON_PRETTY_PRINT));

    // Delete the individual JSON file of the judge
    if ($judgeToDelete !== null) {
        $judgeFile = './src/judges-scores/' . $judgeToDelete['name'] . '.json';
        if (file_exists($judgeFile)) {
            if (unlink($judgeFile)) {
                echo "Judge with ID $targetId and their file '{$judgeToDelete['name']}.json' has been deleted!";
            } else {
                echo "Judge removed, but failed to delete file '{$judgeToDelete['name']}.json'.";
            }
        } else {
            echo "Judge removed, but no file found for '{$judgeToDelete['name']}.json'.";
        }
    } else {
        echo "Judge with ID $targetId not found.";
    }
}
?>
