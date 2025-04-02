<?php
// save_criteria_score.php - Saves individual criteria scores

// Validate input
if (
    !isset($_POST['judge_name']) || 
    !isset($_POST['category']) || 
    !isset($_POST['candidate_id']) || 
    !isset($_POST['criteria_id']) || 
    !isset($_POST['score'])
) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$judgeName = $_POST['judge_name'];
$category = $_POST['category'];
$candidateId = $_POST['candidate_id'];
$criteriaId = $_POST['criteria_id'];
$score = floatval($_POST['score']);

// Validate score (basic validation)
if ($score < 0) {
    echo json_encode(['success' => false, 'message' => 'Score cannot be negative']);
    exit;
}

// Define criteria weights for validation
$criteriaWeights = [
    "production" => [
        "poise" => 40,
        "mastery" => 30,
        "resourcefulness" => 20,
        "overall" => 10
    ],
    "uniform" => [
        "fit" => 30,
        "elegance" => 25,
        "walk" => 25,
        "overall" => 20
    ],
    "advocacy" => [
        "relevance" => 30,
        "presentation" => 25,
        "impact" => 25,
        "creativity" => 20
    ],
    "casual" => [
        "style" => 30,
        "walk" => 30,
        "appropriateness" => 20,
        "overall" => 20
    ],
    "evening" => [
        "elegance" => 30,
        "fit" => 25,
        "walk" => 25,
        "overall" => 20
    ],
    "qa" => [
        "relevance" => 30,
        "clarity" => 25,
        "delivery" => 25,
        "impact" => 20
    ]
];

// Validate against max weight for this criteria
if (isset($criteriaWeights[$category][$criteriaId])) {
    $maxWeight = $criteriaWeights[$category][$criteriaId];
    if ($score > $maxWeight) {
        echo json_encode([
            'success' => false, 
            'message' => "Score exceeds maximum weight of $maxWeight for this criteria"
        ]);
        exit;
    }
}

// Get scores file path
$scoresFile = "./src/judges-scores/{$judgeName}.json";

// Load existing scores
$scores = [];
if (file_exists($scoresFile)) {
    $scoresJson = file_get_contents($scoresFile);
    $scores = json_decode($scoresJson, true);
}

// Initialize category if not exists
if (!isset($scores[$category])) {
    $scores[$category] = [];
}

// Initialize candidate if not exists
if (!isset($scores[$category][$candidateId])) {
    $scores[$category][$candidateId] = [];
}

// Save the score for this criteria
$scores[$category][$candidateId][$criteriaId] = $score;

// Write back to file
$result = file_put_contents($scoresFile, json_encode($scores, JSON_PRETTY_PRINT));

if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to write to file']);
    exit;
}

echo json_encode(['success' => true]);
?>