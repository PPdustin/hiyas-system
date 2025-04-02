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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-bg: #f8f9fa;
            --secondary-bg: #ffffff;
            --primary-text: #212529;
            --secondary-text: #6c757d;
            --accent-color: #007bff;
            --success-color: #28a745;
            --border-color: #dee2e6;
            --header-bg: #e9ecef;
            --winner-bg: rgba(40, 167, 69, 0.1);
            --winner-border: rgba(40, 167, 69, 0.3);
        }
        
        /* Base Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--primary-text);
            background-color: var(--primary-bg);
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-size: 12px;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            background-color: var(--secondary-bg);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.25rem;
        }
        
        /* Typography */
        h1, h2, h3 {
            color: var(--primary-text);
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            page-break-after: avoid;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
        }
        
        h2 {
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 5px;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2em;
            page-break-inside: avoid;
            background-color: var(--secondary-bg);
        }
        
        th, td {
            border: 1px solid var(--border-color);
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: var(--header-bg);
            font-weight: 600;
            color: var(--primary-text);
        }
        
        /* Specific Table Styles */
        .detail-table th {
            font-size: 11px;
        }
        
        .summary-table {
            max-width: 900px;
            margin: 1.5em auto;
        }
        
        .summary-table th {
            text-align: center;
        }
        
        .grand-total-table {
            max-width: 800px;
            margin: 1.5em auto;
        }
        
        /* Cell Styles */
        .candidate-name {
            text-align: left;
            font-weight: 600;
            background-color: rgba(233, 236, 239, 0.5);
        }
        
        .weight-label {
            font-size: 11px;
            color: var(--secondary-text);
            font-weight: normal;
        }
        
        .total-score {
            font-weight: 600;
            background-color: rgba(0, 123, 255, 0.1);
        }
        
        .winner-row {
            background-color: var(--winner-bg);
            border: 1px solid var(--winner-border);
            font-weight: 600;
        }
        
        /* Print Button */
        .print-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: block;
            margin: 20px auto;
            transition: background-color 0.15s ease-in-out;
        }
        
        .print-btn:hover {
            background-color: #0069d9;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Section Headers */
        .section-header {
            background-color: var(--accent-color);
            color: white;
            padding: 8px;
            margin: 1em 0 0.5em;
            border-radius: 4px;
            font-weight: 600;
            font-size: 16px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background-color: white;
                font-size: 11px;
            }
            
            .container {
                width: 100%;
                max-width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
            
            h1 {
                font-size: 18px;
                margin-top: 0;
            }
            
            h2 {
                font-size: 14px;
                margin-top: 1em;
            }
            
            .section-header {
                font-size: 14px;
                background-color: var(--header-bg);
                color: var(--primary-text);
                border: 1px solid var(--border-color);
            }
            
            .print-btn {
                display: none;
            }
            
            table {
                page-break-inside: avoid;
                font-size: 10px;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>HIYAS SCORING RESULTS</h1>
        
        <button class="print-btn" onclick="window.print()">Print Results</button>
        
        <!-- Grand Total Results (Show First) -->
        <div class="section-header">GRAND TOTAL RESULTS</div>
        
        <table class="grand-total-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <th>Pre-Pageant <span class="weight-label">(30%)</span></th>
                    <th>Pageant Night <span class="weight-label">(70%)</span></th>
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
                        <td class="candidate-name"><?= $candidate['number'] ?>. <?= $candidate['name'] ?></td>
                        <td><?= number_format((float)$candidate['prepageant'], 2) ?></td>
                        <td><?= number_format($overallScores[$candidateId] * 0.7, 2) ?></td>
                        <td class="total-score"><?= number_format($score, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Overall Pageant Night Results -->
        <div class="section-header">PAGEANT NIGHT RESULTS <span class="weight-label">(70% of Grand Total)</span></div>
        
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Candidate</th>
                    <?php foreach ($categoryWeights as $category => $weight): ?>
                        <th><?= $categoryNames[$category] ?> <br><span class="weight-label">(<?= $weight ?>%)</span></th>
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
                            <td><?= number_format($categoryAverages[$candidateId][$category], 2) ?></td>
                        <?php endforeach; ?>
                        
                        <td class="total-score"><?= number_format($score, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Category-by-Category Detailed Results -->
        <div class="page-break"></div>
        <div class="section-header">DETAILED CATEGORY RESULTS</div>
        
        <?php foreach ($categoryWeights as $category => $weight): ?>
            <h2><?= $categoryNames[$category] ?> <span class="weight-label">(<?= $weight ?>%)</span></h2>
            
            <table class="detail-table">
                <thead>
                    <tr>
                        <th rowspan="2">Candidate</th>
                        <?php foreach ($judges as $judge): ?>
                            <th colspan="<?= count($criteriaMapping[$category]) + 1 ?>"><?= $judge['name'] ?></th>
                        <?php endforeach; ?>
                        <th rowspan="2">Average</th>
                    </tr>
                    <tr>
                        <?php foreach ($judges as $judge): ?>
                            <?php foreach ($criteriaMapping[$category] as $criterion): ?>
                                <th title="<?= $criterion['name'] ?> (<?= $criterion['weight'] ?>%)">
                                    <?= ucfirst(substr($criterion['id'], 0, 10)) ?>
                                </th>
                            <?php endforeach; ?>
                            <th>Total</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td class="candidate-name"><?= $candidate['number'] ?>. <?= $candidate['name'] ?></td>

                            <?php 
                            $candidateId = $candidate['id'];
                            $overallTotal = 0;
                            ?>

                            <?php foreach ($judges as $judge): ?>
                                <?php 
                                $judgeId = $judge['id'];
                                $scoreData = isset($judgesScores[$judgeId][$category][$candidateId]) ? 
                                    $judgesScores[$judgeId][$category][$candidateId] : [];
                                $judgeTotal = 0;
                                ?>

                                <?php foreach ($criteriaMapping[$category] as $criterion): ?>
                                    <?php 
                                    $score = isset($scoreData[$criterion['id']]) ? $scoreData[$criterion['id']] : 0;
                                    $judgeTotal += $score;
                                    ?>
                                    <td><?= $score ?></td>
                                <?php endforeach; ?>

                                <td class="total-score"><?= $judgeTotal ?></td>
                                <?php $overallTotal += $judgeTotal; ?>
                            <?php endforeach; ?>

                            <td class="total-score">
                                <?= number_format($categoryAverages[$candidateId][$category], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</body>
</html>