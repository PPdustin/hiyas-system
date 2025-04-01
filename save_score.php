<?php
header('Content-Type: application/json');

// Ensure required POST data is present
if (!isset($_POST['candidate_id'], $_POST['score'], $_POST['judge_name'], $_POST['category'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$candidateId = $_POST['candidate_id'];
$score = floatval($_POST['score']);
$judgeName = trim($_POST['judge_name']);
$category = trim($_POST['category']); // Category added

// Validate input
if ($score < 0 || $score > 10 || empty($judgeName) || empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Define JSON file path using the judge's name
$sanitizedJudgeName = preg_replace('/[^a-zA-Z0-9_-]/', '', $judgeName);
$filePath = "./src/judges-scores/" . $sanitizedJudgeName . ".json";

// Ensure the file exists and initialize with an empty structure if needed
if (!file_exists($filePath) || filesize($filePath) == 0) {
    file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
}

// Load existing scores
$scores = json_decode(file_get_contents($filePath), true);

// Initialize category if it doesn’t exist
if (!isset($scores[$category])) {
    $scores[$category] = [];
}

// Update the candidate's score under the specific category
$scores[$category][$candidateId] = $score;

// Save back to JSON file
file_put_contents($filePath, json_encode($scores, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
?>
