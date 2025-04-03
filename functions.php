<?php
/**
 * Calculate the scoring progress percentage for a judge
 * 
 * @param string $judgeName The name of the judge
 * @param array $candidates Array of candidate objects
 * @param array $criteriaMapping Array mapping categories to their criteria
 * @return float The percentage of progress (0-100)
 */
function calculateJudgeProgress($judgeName, $candidates) {

    $criteriaMapping = [
        "cultural" => [
            ["id" => "statement", "name" => "Statement/Appeal", "weight" => 40],
            ["id" => "appreciation", "name" => "Cultural Appreciation", "weight" => 40],
            ["id" => "stage", "name" => "Stage Presence", "weight" => 10],
            ["id" => "overall", "name" => "Overall Impact", "weight" => 10]
        ],
        "sports" => [
            ["id" => "presence", "name" => "Presence and Confidence", "weight" => 40],
            ["id" => "beauty", "name" => "Beauty (Uniqueness and Appropriateness)", "weight" => 30],
            ["id" => "bearing", "name" => "Bearing of Attire", "weight" => 20],
            ["id" => "overall", "name" => "Overall Impact", "weight" => 10]
        ],
        "advocacy" => [
            ["id" => "content", "name" => "Content/Topic/Speech Development", "weight" => 40],
            ["id" => "fluency", "name" => "Fluency and Delivery", "weight" => 20],
            ["id" => "relevance", "name" => "Relevance of the Campaign", "weight" => 30],
            ["id" => "overall", "name" => "Overall Impact", "weight" => 10]
        ],
        "casual" => [
            ["id" => "poise", "name" => "Poise, Bearing and Stage Presence", "weight" => 40],
            ["id" => "mastery", "name" => "Mastery and Gracefulness", "weight" => 30],
            ["id" => "resourcefulness", "name" => "Resourcefulness (Props and Materials)", "weight" => 20],
            ["id" => "overall", "name" => "Overall Impact", "weight" => 10]
        ],
        "evening" => [
            ["id" => "poise", "name" => "Poise and Bearing", "weight" => 40],
            ["id" => "assertiveness", "name" => "Assertiveness", "weight" => 30],
            ["id" => "elegance", "name" => "Elegance", "weight" => 20],
            ["id" => "overall", "name" => "Overall Impact", "weight" => 10]
        ],
        "qa" => [
            ["id" => "content", "name" => "Content/Substance/Relevance", "weight" => 40],
            ["id" => "fluency", "name" => "Fluency", "weight" => 20],
            ["id" => "language", "name" => "Mastery of Language (Speech, Vocabulary, Grammar)", "weight" => 30],
            ["id" => "personality", "name" => "Personality and Emotional Control", "weight" => 10]
        ]
    ];


    // Path to the judge's JSON file
    $filePath = "./src/judges-scores/{$judgeName}.json";
    
    // Check if the file exists
    if (!file_exists($filePath)) {
        return 0; // Return 0% if no file exists
    }
    
    // Read and parse the JSON file
    $judgeData = json_decode(file_get_contents($filePath), true);
    if (!$judgeData) {
        return 0; // Return 0% if file is empty or not valid JSON
    }
    
    // Get the categories from the criteria mapping
    $categories = array_keys($criteriaMapping);
    $totalCategories = count($categories);
    
    // Count the total number of candidates
    $totalCandidates = count($candidates);
    
    // Calculate the maximum possible scores
    $maxPossibleScores = 0;
    foreach ($categories as $category) {
        $criteriaCount = count($criteriaMapping[$category]);
        $maxPossibleScores += ($criteriaCount * $totalCandidates);
    }
    
    // Count the actual scores entered
    $actualScores = 0;
    
    // Loop through each category
    foreach ($categories as $category) {
        // Check if this category exists in the judge's data
        if (!isset($judgeData[$category])) {
            continue;
        }
        
        // Loop through each candidate in this category
        foreach ($judgeData[$category] as $candidateId => $criteriaScores) {
            // Count each criterion that has been scored
            foreach ($criteriaScores as $criterion => $score) {
                if (isset($score) && $score !== null) {
                    $actualScores++;
                }
            }
        }
    }
    
    // Calculate the percentage
    $progressPercentage = ($actualScores / $maxPossibleScores) * 100;
    
    // Round to 2 decimal places
    return round($progressPercentage, 2);
}

/**
 * Example usage with PHP representation of the JavaScript variables:
 * 
 * $candidates = [
 *     ["id" => "1", "name" => "CEAT", "prepageant" => "30", "number" => "1"],
 *     ["id" => "2", "name" => "CAS", "prepageant" => "29", "number" => "2"],
 *     ["id" => "3", "name" => "BC", "prepageant" => "29", "number" => "3"],
 *     ["id" => "4", "name" => "CHS", "prepageant" => "29", "number" => "4"]
 * ];
 * 
 * $criteriaMapping = [
 *     "cultural" => [
 *         ["id" => "statement", "name" => "Statement/Appeal", "weight" => 40],
 *         // ... other criteria
 *     ],
 *     // ... other categories
 * ];
 * 
 * $progress = calculateJudgeProgress("Dustin", $candidates, $criteriaMapping);
 * echo "Judge Dustin's scoring progress: {$progress}%";
 */
?>