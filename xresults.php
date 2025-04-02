<?php
// Define paths to JSON files
$candidatesFile = './src/candidates.json';
$judgesFile = './src/judges.json';
$judgesScoresDir = './src/judges-scores/';

// Load candidates and judges data
$candidates = json_decode(file_get_contents($candidatesFile), true);
$judges = json_decode(file_get_contents($judgesFile), true);

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
    "cultural" => "Cultural Attire",
    "sports" => "Sports Wear",
    "advocacy" => "Advocacy",
    "casual" => "Casual Wear",
    "evening" => "Evening Gown",
    "qa" => "Question and Answer"
];

// Load all judges' scores
$judgesScores = [];
foreach ($judges as $judge) {
    $scoreFile = $judgesScoresDir . $judge['name'] . '.json';
    if (file_exists($scoreFile)) {
        $judgesScores[$judge['name']] = json_decode(file_get_contents($scoreFile), true);
    }
}

// Calculate weighted scores and totals
function calculateWeightedScore($score, $weight) {
    return ($score * $weight) / 100;
}

// Function to calculate candidate's score for a specific category
function calculateCategoryScore($candidateId, $category, $judgeScores, $criteriaMapping) {
    $totalWeightedScore = 0;
    
    if (isset($judgeScores[$category][$candidateId])) {
        foreach ($criteriaMapping[$category] as $criteria) {
            $criteriaId = $criteria['id'];
            $weight = $criteria['weight'];
            
            if (isset($judgeScores[$category][$candidateId][$criteriaId])) {
                $score = $judgeScores[$category][$candidateId][$criteriaId];
                $totalWeightedScore += calculateWeightedScore($score, $weight);
            }
        }
    }
    
    return $totalWeightedScore;
}

// Calculate overall scores
$overallScores = [];
foreach ($candidates as $candidate) {
    $candidateId = $candidate['number']; // Using number as ID
    $overallScores[$candidateId] = [
        'name' => $candidate['name'],
        'number' => $candidate['number'],
        'prepageant' => isset($candidate['prepageant']) ? (float)$candidate['prepageant'] : 0,
        'categories' => [],
        'judges' => [],
        'total' => 0
    ];
    
    // Pre-pageant score
    $overallScores[$candidateId]['total'] += $overallScores[$candidateId]['prepageant'];
    
    // Calculate scores per judge and category
    foreach ($judges as $judge) {
        $judgeName = $judge['name'];
        $overallScores[$candidateId]['judges'][$judgeName] = ['categories' => [], 'total' => 0];
        
        if (isset($judgesScores[$judgeName])) {
            foreach ($criteriaMapping as $categoryId => $criteria) {
                $categoryScore = calculateCategoryScore($candidateId, $categoryId, $judgesScores[$judgeName], $criteriaMapping);
                $overallScores[$candidateId]['judges'][$judgeName]['categories'][$categoryId] = $categoryScore;
                $overallScores[$candidateId]['judges'][$judgeName]['total'] += $categoryScore;
            }
        }
    }
    
    // Calculate average scores per category across all judges
    foreach ($criteriaMapping as $categoryId => $criteria) {
        $categoryTotal = 0;
        $judgeCount = 0;
        
        foreach ($judges as $judge) {
            $judgeName = $judge['name'];
            if (isset($overallScores[$candidateId]['judges'][$judgeName]['categories'][$categoryId])) {
                $categoryTotal += $overallScores[$candidateId]['judges'][$judgeName]['categories'][$categoryId];
                $judgeCount++;
            }
        }
        
        $categoryAverage = $judgeCount > 0 ? $categoryTotal / $judgeCount : 0;
        $overallScores[$candidateId]['categories'][$categoryId] = $categoryAverage;
        $overallScores[$candidateId]['total'] += $categoryAverage;
    }
}

// Sort candidates by total score (descending)
usort($overallScores, function($a, $b) {
    return $b['total'] <=> $a['total'];
});

// Function to get detailed score breakdown
function getDetailedScores($candidateId, $category, $judgeName, $judgesScores, $criteriaMapping) {
    $details = [];
    
    if (isset($judgesScores[$judgeName][$category][$candidateId])) {
        foreach ($criteriaMapping[$category] as $criteria) {
            $criteriaId = $criteria['id'];
            $weight = $criteria['weight'];
            
            if (isset($judgesScores[$judgeName][$category][$candidateId][$criteriaId])) {
                $rawScore = $judgesScores[$judgeName][$category][$candidateId][$criteriaId];
                $weightedScore = calculateWeightedScore($rawScore, $weight);
                
                $details[$criteriaId] = [
                    'raw' => $rawScore,
                    'weighted' => $weightedScore
                ];
            }
        }
    }
    
    return $details;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pageant Scoring Results</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Montserrat:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Base styles according to style guide */
        :root {
            --primary-bg: #f8f9fa;
            --secondary-bg: #ffffff;
            --primary-text: #212529;
            --secondary-text: #6c757d;
            --accent-color: #007bff;
            --success-color: #28a745;
            --border-color: #dee2e6;
        }
        
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
        
        .btn, button, label {
            font-family: 'Montserrat', sans-serif;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background-color: var(--secondary-bg);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        th, td {
            border: 1px solid var(--border-color);
            padding: 8px;
            text-align: center;
        }
        
        th {
            background-color: var(--accent-color);
            color: white;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: rgba(0,0,0,0.02);
        }
        
        .top-score {
            background-color: rgba(40, 167, 69, 0.1);
            font-weight: bold;
        }
        
        .candidate-name {
            font-weight: bold;
        }
        
        /* Print styles */
        @media print {
            body {
                font-size: 12px;
                background-color: white;
                padding: 0;
                margin: 0;
            }
            
            .card {
                box-shadow: none;
                margin-bottom: 10px;
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
            
            table {
                font-size: 10px;
                page-break-inside: avoid;
                box-shadow: none;
            }
            
            th {
                background-color: #f1f1f1 !important;
                color: black !important;
            }
            
            /* Force a page break between major sections */
            .page-break {
                page-break-after: always;
            }
            
            /* Minimize margins */
            @page {
                margin: 1cm;
            }
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.5rem 1rem;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }
        
        .btn-primary {
            color: #fff;
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        /* Navigation tabs */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .tab {
            padding: 8px 16px;
            cursor: pointer;
            border: 1px solid transparent;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            margin-right: 5px;
            margin-bottom: -1px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .tab.active {
            border-color: var(--border-color);
            border-bottom-color: var(--secondary-bg);
            background-color: var(--secondary-bg);
            color: var(--accent-color);
        }
        
        .tab:hover:not(.active) {
            background-color: rgba(0,0,0,0.02);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Rankings */
        .rank {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: white;
            text-align: center;
            font-weight: bold;
            margin-right: 8px;
        }
        
        .rank-1 {
            background-color: gold;
            color: black;
        }
        
        .rank-2 {
            background-color: silver;
            color: black;
        }
        
        .rank-3 {
            background-color: #cd7f32; /* bronze */
            color: white;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        .mt-4 {
            margin-top: 20px;
        }
        
        .float-right {
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="text-center">Pageant Scoring Results</h1>
            <p class="text-center">
                Total Candidates: <?= count($candidates) ?> | 
                Total Judges: <?= count($judges) ?>
            </p>
            
            <div class="no-print">
                <button class="btn btn-primary" onclick="window.print()">Print Results</button>
                
                <div class="tabs mt-4">
                    <div class="tab active" onclick="showTab('overall')">Overall Results</div>
                    <?php foreach ($categoryNames as $id => $name): ?>
                    <div class="tab" onclick="showTab('<?= $id ?>')"><?= $name ?></div>
                    <?php endforeach; ?>
                    <div class="tab" onclick="showTab('detailed')">Detailed Breakdown</div>
                </div>
            </div>
            
            <!-- Overall Results Tab -->
            <div id="overall" class="tab-content active">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">Rank</th>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Candidate</th>
                                <th rowspan="2">Pre-pageant</th>
                                <?php foreach ($categoryNames as $id => $name): ?>
                                <th><?= $name ?></th>
                                <?php endforeach; ?>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <?php foreach ($categoryNames as $id => $name): ?>
                                <th>Score</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overallScores as $index => $candidate): ?>
                            <tr>
                                <td>
                                    <span class="rank <?= $index < 3 ? 'rank-'.($index+1) : '' ?>">
                                        <?= $index + 1 ?>
                                    </span>
                                </td>
                                <td><?= $candidate['number'] ?></td>
                                <td class="candidate-name"><?= $candidate['name'] ?></td>
                                <td><?= number_format($candidate['prepageant'], 2) ?></td>
                                <?php foreach ($criteriaMapping as $categoryId => $criteria): ?>
                                <td><?= isset($candidate['categories'][$categoryId]) ? number_format($candidate['categories'][$categoryId], 2) : '-' ?></td>
                                <?php endforeach; ?>
                                <td class="top-score"><?= number_format($candidate['total'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Category Tabs -->
            <?php foreach ($categoryNames as $categoryId => $categoryName): ?>
            <div id="<?= $categoryId ?>" class="tab-content">
                <h2><?= $categoryName ?> Results</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">Rank</th>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Candidate</th>
                                <?php foreach ($judges as $judge): ?>
                                <th><?= $judge['name'] ?></th>
                                <?php endforeach; ?>
                                <th rowspan="2">Average</th>
                            </tr>
                            <tr>
                                <?php foreach ($judges as $judge): ?>
                                <th>Score</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Create a copy for sorting by this category
                            $categorySorted = $overallScores;
                            usort($categorySorted, function($a, $b) use ($categoryId) {
                                $scoreA = isset($a['categories'][$categoryId]) ? $a['categories'][$categoryId] : 0;
                                $scoreB = isset($b['categories'][$categoryId]) ? $b['categories'][$categoryId] : 0;
                                return $scoreB <=> $scoreA;
                            });
                            ?>
                            
                            <?php foreach ($categorySorted as $index => $candidate): ?>
                            <tr>
                                <td>
                                    <span class="rank <?= $index < 3 ? 'rank-'.($index+1) : '' ?>">
                                        <?= $index + 1 ?>
                                    </span>
                                </td>
                                <td><?= $candidate['number'] ?></td>
                                <td class="candidate-name"><?= $candidate['name'] ?></td>
                                <?php foreach ($judges as $judge): ?>
                                <td>
                                    <?php 
                                    $judgeName = $judge['name'];
                                    $score = isset($candidate['judges'][$judgeName]['categories'][$categoryId]) 
                                        ? number_format($candidate['judges'][$judgeName]['categories'][$categoryId], 2) 
                                        : '-';
                                    echo $score;
                                    ?>
                                </td>
                                <?php endforeach; ?>
                                <td class="top-score">
                                    <?= isset($candidate['categories'][$categoryId]) 
                                        ? number_format($candidate['categories'][$categoryId], 2) 
                                        : '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <h3>Criteria Breakdown for <?= $categoryName ?></h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Criteria</th>
                                <th>Weight</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($criteriaMapping[$categoryId] as $criteria): ?>
                            <tr>
                                <td><?= $criteria['id'] ?></td>
                                <td><?= $criteria['weight'] ?>%</td>
                                <td style="text-align: left"><?= $criteria['name'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Detailed Breakdown Tab -->
            <div id="detailed" class="tab-content">
                <h2>Detailed Score Breakdown</h2>
                
                <?php foreach ($candidates as $candidate): ?>
                <?php $candidateId = $candidate['number']; ?>
                <div class="card">
                    <h3>Candidate #<?= $candidateId ?>: <?= $candidate['name'] ?></h3>
                    
                    <?php foreach ($categoryNames as $categoryId => $categoryName): ?>
                    <h4><?= $categoryName ?></h4>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Judge</th>
                                    <?php foreach ($criteriaMapping[$categoryId] as $criteria): ?>
                                    <th colspan="2"><?= $criteria['name'] ?> (<?= $criteria['weight'] ?>%)</th>
                                    <?php endforeach; ?>
                                    <th>Total</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <?php foreach ($criteriaMapping[$categoryId] as $criteria): ?>
                                    <th>Raw</th>
                                    <th>Weighted</th>
                                    <?php endforeach; ?>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($judges as $judge): ?>
                                <?php 
                                $judgeName = $judge['name'];
                                $detailedScores = isset($judgesScores[$judgeName]) 
                                    ? getDetailedScores($candidateId, $categoryId, $judgeName, $judgesScores, $criteriaMapping) 
                                    : [];
                                $totalScore = isset($overallScores[$candidateId]['judges'][$judgeName]['categories'][$categoryId])
                                    ? $overallScores[$candidateId]['judges'][$judgeName]['categories'][$categoryId]
                                    : 0;
                                ?>
                                <tr>
                                    <td><?= $judgeName ?></td>
                                    <?php foreach ($criteriaMapping[$categoryId] as $criteria): ?>
                                    <?php 
                                    $criteriaId = $criteria['id'];
                                    $rawScore = isset($detailedScores[$criteriaId]) ? $detailedScores[$criteriaId]['raw'] : '-';
                                    $weightedScore = isset($detailedScores[$criteriaId]) ? number_format($detailedScores[$criteriaId]['weighted'], 2) : '-';
                                    ?>
                                    <td><?= $rawScore ?></td>
                                    <td><?= $weightedScore ?></td>
                                    <?php endforeach; ?>
                                    <td><?= number_format($totalScore, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td><strong>Average</strong></td>
                                    <?php 
                                    $colCount = count($criteriaMapping[$categoryId]) * 2 + 1;
                                    $avgScore = isset($overallScores[$candidateId]['categories'][$categoryId])
                                        ? number_format($overallScores[$candidateId]['categories'][$categoryId], 2)
                                        : '-';
                                    ?>
                                    <td colspan="<?= $colCount ?>"><strong><?= $avgScore ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card mt-4">
                <p class="mb-0 text-center">© <?= date('Y') ?> Pageant Scoring System</p>
            </div>
        </div>
    </div>
    
    <script>
        // Tab functionality
        function showTab(tabId) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Deactivate all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Activate the tab button
            const activeTab = Array.from(tabs).find(tab => {
                return tab.getAttribute('onclick').includes(`'${tabId}'`);
            });
            
            if (activeTab) {
                activeTab.classList.add('active');
            }
        }
    </script>
</body>
</html>