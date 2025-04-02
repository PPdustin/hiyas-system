<?php
// Define paths to JSON files
$candidatesFile = './src/candidates.json';
$judgesFile = './src/judges.json';
$judgesScoresPath = './src/judges-scores/';

// Load candidates and judges data
$candidates = json_decode(file_get_contents($candidatesFile), true);
$judges = json_decode(file_get_contents($judgesFile), true);

// Define category weights for pageant night (70% of grand total)
$categoryWeights = [
    'cultural' => 10,
    'sports' => 10,
    'advocacy' => 15,
    'casual' => 15,
    'evening' => 20,
    'qa' => 30
];

// Define criteria mapping with weights
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

// Category display names
$categoryNames = [
    'cultural' => 'Cultural Attire',
    'sports' => 'Sports Attire',
    'advocacy' => 'Advocacy Campaign Speech/Video',
    'casual' => 'Casual Attire/Production Number',
    'evening' => 'Evening Gown and Formal Wear',
    'qa' => 'Final Q and A'
];

// Load all judges' scores
$judgesScores = [];
foreach ($judges as $judge) {
    $judgeScoreFile = $judgesScoresPath . $judge['name'] . '.json';
    if (file_exists($judgeScoreFile)) {
        $judgesScores[$judge['id']] = json_decode(file_get_contents($judgeScoreFile), true);
    }
}

// Function to calculate weighted score for a criterion
function calculateWeightedScore($score, $weight) {
    return ($score * $weight) / 100;
}

// Function to calculate overall score for a category
function calculateCategoryScore($candidateScores, $criteria) {
    $totalScore = 0;
    $totalWeight = 0;
    
    foreach ($criteria as $criterion) {
        if (isset($candidateScores[$criterion['id']])) {
            $totalScore += $candidateScores[$criterion['id']];
            $totalWeight += $criterion['weight'];
        }
    }
    
    // Normalize if weights don't add up to 100
    if ($totalWeight > 0 && $totalWeight != 100) {
        $totalScore = ($totalScore * 100) / $totalWeight;
    }
    
    return $totalScore;
}

// Function to calculate average score across all judges for a candidate in a category
function calculateAverageScore($candidateId, $category, $judgesScores, $criteria) {
    $totalScore = 0;
    $judgeCount = 0;
    
    foreach ($judgesScores as $judgeId => $judgeScores) {
        if (isset($judgeScores[$category][$candidateId])) {
            $totalScore += calculateCategoryScore($judgeScores[$category][$candidateId], $criteria);
            $judgeCount++;
        }
    }
    
    return $judgeCount > 0 ? $totalScore / $judgeCount : 0;
}

// Compile all scores
$compiledScores = [];
$categoryAverages = [];
$overallScores = [];

foreach ($candidates as $candidate) {
    $candidateId = $candidate['id'];
    $compiledScores[$candidateId] = [];
    $categoryAverages[$candidateId] = [];
    $overallTotal = 0;
    
    foreach ($categoryWeights as $category => $weight) {
        $categoryAverages[$candidateId][$category] = calculateAverageScore(
            $candidateId, 
            $category, 
            $judgesScores, 
            $criteriaMapping[$category]
        );
        
        // Calculate weighted category score for overall total
        $weightedCategoryScore = ($categoryAverages[$candidateId][$category] * $weight) / 100;
        $overallTotal += $weightedCategoryScore;
    }
    
    // Store overall pageant night score (70% of grand total)
    $overallScores[$candidateId] = $overallTotal;
}

// Calculate grand total scores (prepageant 30% + pageant night 70%)
$grandTotalScores = [];
foreach ($candidates as $candidate) {
    $candidateId = $candidate['id'];
    $prepageantScore = isset($candidate['prepageant']) ? (float)$candidate['prepageant'] : 0; // 30% weight
    $pageantNightScore = $overallScores[$candidateId] * 0.7; // 70% weight
    $grandTotalScores[$candidateId] = $prepageantScore + $pageantNightScore;
}

// Sort candidates by grand total scores (descending)
arsort($grandTotalScores);

// Function to get formatted ordinal number
function getOrdinal($number) {
    $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $suffixes[$number % 10];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiyas Scoring Results</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Montserrat:wght@500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Color Palette */
        :root {
            --primary-bg: #f8f9fa;
            --secondary-bg: #ffffff;
            --primary-text: #212529;
            --secondary-text: #6c757d;
            --accent-color: #007bff;
            --success-color: #28a745;
            --border-color: #dee2e6;
            --header-bg: #e9ecef;
        }
        
        /* Base Styles */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--primary-text);
            background-color: var(--primary-bg);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            margin-top: 0;
        }
        
        button, .btn {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        /* Page Layout */
        .container {
            max-width: 2000px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--secondary-bg);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }
        
        /* Table Styles */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            page-break-inside: avoid;
        }
        
        .results-table th,
        .results-table td {
            padding: 8px 6px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .results-table th {
            background-color: var(--header-bg);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.85rem;
        }
        
        .results-table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .category-header {
            background-color: var(--accent-color) !important;
            color: white;
            padding: 10px !important;
            text-align: left !important;
        }
        
        .candidate-name {
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
            background-color: #f1f3f5;
        }
        
        .judge-name {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        .score-cell {
            font-family: 'Montserrat', sans-serif;
        }
        
        .average-row {
            background-color: #e9ecef;
            font-weight: 600;
        }
        
        .grand-total-table {
            width: 100%;
            max-width: 800px;
            margin: 2rem auto;
            border-collapse: collapse;
        }
        
        .grand-total-table th,
        .grand-total-table td {
            padding: 10px;
            border: 1px solid var(--border-color);
        }
        
        .grand-total-table th {
            background-color: var(--header-bg);
        }
        
        .winner-row {
            background-color: rgba(40, 167, 69, 0.1);
            font-weight: bold;
        }
        
        .weight-cell {
            font-style: italic;
            color: var(--secondary-text);
            font-size: 0.85rem;
        }
        
        .print-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 20px;
            transition: background-color 0.2s;
        }
        
        .print-btn:hover {
            background-color: #0069d9;
        }
        
        /* Print styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            
            .print-btn {
                display: none;
            }
            
            /* .page-break {
                page-break-before: always;
            } */
            
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .results-table th,
            .results-table td {
                padding: 5px 3px;
                font-size: 0.8rem;
            }
            
            @page {
                size: portrait;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hiyas Scoring Results</h1>
        
        <button class="print-btn" onclick="window.print()">Print Results</button>
        
        <!-- Category-by-Category Results -->
        <?php foreach ($categoryWeights as $category => $weight): ?>
            <h2><?= $categoryNames[$category] ?> <span class="weight-cell">(<?= $weight ?>%)</span></h2>
            
            <table class="results-table">
                <thead>
                    <tr>
                        <th rowspan="2">Candidate</th>
                        <?php foreach ($judges as $judge): ?>
                            <th colspan="<?= count($criteriaMapping[$category]) + 1 ?>"><?= $judge['name'] ?></th> <!-- +1 for total column -->
                        <?php endforeach; ?>
                        <th rowspan="2">Average</th>
                        <th rowspan="2">Total</th> <!-- Overall total column -->
                    </tr>
                    <tr>
                        <?php foreach ($judges as $judge): ?>
                            <?php foreach ($criteriaMapping[$category] as $criterion): ?>
                                <th title="<?= $criterion['name'] ?> (<?= $criterion['weight'] ?>%)">
                                    <?= substr($criterion['id'], 0, 20) ?><?= (strlen($criterion['id']) > 20) ? '...' : '' ?>
                                </th>
                            <?php endforeach; ?>
                            <th><strong>Total</strong></th> <!-- Total per judge -->
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td class="candidate-name"><?= $candidate['number'] ?>. <?= $candidate['name'] ?></td>

                            <?php 
                            $candidateId = $candidate['id'];
                            $overallTotal = 0; // Initialize overall total for the candidate
                            ?>

                            <?php foreach ($judges as $judge): ?>
                                <?php 
                                $judgeId = $judge['id'];
                                $scoreData = isset($judgesScores[$judgeId][$category][$candidateId]) ? 
                                    $judgesScores[$judgeId][$category][$candidateId] : [];
                                $judgeTotal = 0; // Initialize total for the specific judge
                                ?>

                                <?php foreach ($criteriaMapping[$category] as $criterion): ?>
                                    <?php 
                                    $score = isset($scoreData[$criterion['id']]) ? $scoreData[$criterion['id']] : 0;
                                    $judgeTotal += $score; // Sum up scores for the judge
                                    ?>
                                    <td class="score-cell"><?= $score ?></td>
                                <?php endforeach; ?>

                                <td class="score-cell"><strong><?= $judgeTotal ?></strong></td> <!-- Judge total -->
                                <?php $overallTotal += $judgeTotal; ?> <!-- Add judge's total to overall total -->
                            <?php endforeach; ?>

                            <td class="score-cell">
                                <?= number_format($categoryAverages[$candidateId][$category], 2) ?>
                            </td>
                            <td class="score-cell total-score"><strong><?= $overallTotal ?></strong></td> <!-- Overall total -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        
        <!-- Overall Pageant Night Results -->
        <h2 class="page-break">Pageant Night Results <span class="weight-cell">(70% of Grand Total)</span></h2>
        
        <table class="results-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <?php foreach ($categoryWeights as $category => $weight): ?>
                        <th><?= $categoryNames[$category] ?> <span class="weight-cell">(<?= $weight ?>%)</span></th>
                    <?php endforeach; ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Sort candidates by overall score for ranking
                $sortedOverallScores = $overallScores;
                arsort($sortedOverallScores);
                $rank = 1;
                
                foreach ($sortedOverallScores as $candidateId => $score): 
                    // Find candidate data
                    $candidate = null;
                    foreach ($candidates as $c) {
                        if ($c['id'] == $candidateId) {
                            $candidate = $c;
                            break;
                        }
                    }
                    if (!$candidate) continue;
                ?>
                    <tr>
                        <td><?= getOrdinal($rank++) ?></td>
                        <td class="candidate-name"><?= $candidate['number'] ?>. <?= $candidate['name'] ?></td>
                        
                        <?php foreach ($categoryWeights as $category => $weight): ?>
                            <td class="score-cell">
                                <?= number_format($categoryAverages[$candidateId][$category], 2) ?>
                            </td>
                        <?php endforeach; ?>
                        
                        <td class="score-cell"><?= number_format($score, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Grand Total Results -->
        <h2>Grand Total Results</h2>
        
        <table class="grand-total-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <th>Pre-Pageant <span class="weight-cell">(30%)</span></th>
                    <th>Pageant Night <span class="weight-cell">(70%)</span></th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($grandTotalScores as $candidateId => $score): 
                    // Find candidate data
                    $candidate = null;
                    foreach ($candidates as $c) {
                        if ($c['id'] == $candidateId) {
                            $candidate = $c;
                            break;
                        }
                    }
                    if (!$candidate) continue;
                    
                    // Determine if this is the winner (rank 1)
                    $isWinner = $rank === 1;
                ?>
                    <tr class="<?= $isWinner ? 'winner-row' : '' ?>">
                        <td><?= getOrdinal($rank++) ?></td>
                        <td><?= $candidate['number'] ?>. <?= $candidate['name'] ?></td>
                        <td><?= number_format((float)$candidate['prepageant'], 2) ?></td>
                        <td><?= number_format($overallScores[$candidateId] * 0.7, 2) ?></td>
                        <td><?= number_format($score, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>