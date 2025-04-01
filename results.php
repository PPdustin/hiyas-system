<?php
    $code = $_GET['code'] ?? "";
    if ($code != 'jpg2025') {
        ?>
            <h1>YOU ARE NOT AUTHORIZED</h1>
        <?php
            exit; // STOP execution here if unauthorized
    }


    $jsonData = file_get_contents('./src/candidates.json');
    $candidatesArray = json_decode($jsonData, true);
    
    // Initialize an empty array for candidates
    $candidates = [];
    
    // Assign candidates to the correct index based on their ID
    foreach ($candidatesArray as $candidate) {
        $candidates[(int)$candidate['id']] = $candidate['name'];
    }
    
    // Convert PHP array to JavaScript format
    $jsArray = json_encode(array_values($candidates));
    
    // Read the JSON file for judges
    $judgesData = file_get_contents('./src/judges.json');
    $judgesArray = json_decode($judgesData, true);
    
    // Define categories
    $categories = ["production", "uniform", "advocacy", "casual", "evening", "qa"];
    
    // Initialize scores array
    $scores = array_fill_keys(array_keys($categories), []);
    
    // Load scores from each judge's file
    foreach ($judgesArray as $judge) {
        $judgeName = $judge['name'];
        $judgeFile = "./src/judges-scores/$judgeName.json";
        
        if (file_exists($judgeFile)) {
            $judgeScores = json_decode(file_get_contents($judgeFile), true);
            
            foreach ($categories as $index => $category) {
                if (!isset($scores[$index][$judgeName])) {
                    $scores[$index][$judgeName] = [];
                }
                
                foreach ($candidatesArray as $candidate) {
                    $candidateId = $candidate['id'];
                    $scores[$index][$judgeName][] = $judgeScores[$category][$candidateId] ?? null;
                }
            }
        }
    }
    
    // Convert PHP arrays to JavaScript format
    $jsScores = json_encode($scores);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Page</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #ffffff;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        .category-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
            background: #e9ecef;
        }
        .highlight {
            background: #ffc107;
            font-weight: bold;
        }
        .not-scored {
            color: #dc3545;
            font-weight: bold;
        }
        .overall-results {
            background: #28a745;
            color: white;
            font-weight: bold;
        }
        .overall-results-body {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Judging Results</h1>

    <div id="results"></div>
    <div id="overall-results"></div>

    <script>
    const categories = [
        { name: "Production Number/Creative Cultural Attire", weight: 0.10 },
        { name: "Uniform Attire", weight: 0.10 },
        { name: "Advocacy Campaign Speech/Video", weight: 0.15 },
        { name: "Casual Attire", weight: 0.15 },
        { name: "Evening Gown and Formal Wear", weight: 0.20 },
        { name: "Final Q and A", weight: 0.30 }
    ];

    const candidates = <?php echo $jsArray; ?>;;
    
    // const scores = {
    //     0: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, null]},
    //     1: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, 10]},
    //     2: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, 10]},
    //     3: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, 10]},
    //     4: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, 10]},
    //     5: { "Judge 1": [10, 10, 10, 10], "Judge 2": [10, 10, 10, 10]},
    // };

    const scores = <?php echo $jsScores; ?>;
    console.log(scores);

    let weightedScores = new Array(candidates.length).fill(0);
    const resultsContainer = document.getElementById("results");

    // Generate category tables with placements
    categories.forEach((category, categoryIndex) => {
        const judges = Object.keys(scores[categoryIndex]);
        let tableHTML = `
            <div class="category-title">${category.name} (${(category.weight * 100).toFixed(0)}%)</div>
            <table>
                <thead>
                    <tr><th>Judge</th>${candidates.map(c => `<th>${c}</th>`).join('')}</tr>
                </thead>
                <tbody>`;

        // Judges' scores
        judges.forEach(judge => {
            tableHTML += `<tr><td>${judge}</td>`;
            scores[categoryIndex][judge].forEach((score, i) => {
                tableHTML += `<td>${score !== null ? score.toFixed(1) : '<span class="not-scored">N/A</span>'}</td>`;
            });
            tableHTML += `</tr>`;
        });

        // Category totals
        const categoryTotals = candidates.map((_, i) => {
            const validScores = judges
                .map(judge => scores[categoryIndex][judge][i])
                .filter(score => score !== null);
            
            return validScores.length > 0 
                ? validScores.reduce((a, b) => a + b, 0) / validScores.length
                : null;
        });

        // Category total row
        tableHTML += `<tr class="highlight"><td>Category Total</td>`;
        categoryTotals.forEach((total, i) => {
            if (total !== null) {
                weightedScores[i] += total * category.weight;
                tableHTML += `<td>${total.toFixed(1)}</td>`;
            } else {
                tableHTML += `<td class="not-scored">N/A</td>`;
            }
        });
        tableHTML += `</tr>`;

        // Calculate category placements
        const validEntries = categoryTotals
            .map((total, index) => ({ total, index }))
            .filter(entry => entry.total !== null);

        validEntries.sort((a, b) => b.total - a.total);
        
        const placements = new Array(candidates.length).fill('N/A');
        let currentRank = 1;
        validEntries.forEach((entry, i) => {
            if (i > 0 && entry.total === validEntries[i-1].total) {
                placements[entry.index] = placements[validEntries[i-1].index];
            } else {
                placements[entry.index] = currentRank;
            }
            currentRank++;
        });

        // Placement row
        tableHTML += `<tr class="highlight"><td>Placement</td>`;
        // tableHTML += `<tr class="placement-row"><td>Placement</td>`;
        placements.forEach(rank => {
            tableHTML += `<td>${rank === 'N/A' ? 'N/A' : formatPlacement(rank)}</td>`;
        });
        tableHTML += `</tr></tbody></table>`;
        
        resultsContainer.innerHTML += tableHTML;
    });

    // Final overall results
    const overallHTML = `
        <div class="category-title overall-results">Final Overall Results</div>
        <table>
            <thead><tr><th>Candidate</th><th>Overall Score</th><th>Placement</th></tr></thead>
            <tbody>
                ${candidates.map((candidate, i) => `
                    <tr class="overall-results-body">
                        <td>${candidate}</td>
                        <td>${weightedScores[i].toFixed(2)}</td>
                        <td>${formatPlacement(getOverallRank(weightedScores, i))}</td>
                    </tr>`
                ).join('')}
            </tbody>
        </table>`;
    
    document.getElementById("overall-results").innerHTML = overallHTML;

    // Helper functions
    function getOverallRank(scores, index) {
        const sorted = [...scores].map((s, i) => ({ score: s, index: i }))
                         .sort((a, b) => b.score - a.score);
        return sorted.findIndex(item => item.index === index) + 1;
    }

    function formatPlacement(rank) {
        if (rank === 'N/A') return 'N/A';
        const v = rank % 100;
        if (v >= 11 && v <= 13) return `${rank}th`;
        switch(rank % 10) {
            case 1: return `${rank}st`;
            case 2: return `${rank}nd`;
            case 3: return `${rank}rd`;
            default: return `${rank}th`;
        }
    }
</script>

</body>
</html>
