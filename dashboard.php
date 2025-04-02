<?php include "authorizer.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiyas System - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Montserrat:wght@500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #f8f9fa;
            --secondary-bg: #ffffff;
            --primary-text: #212529;
            --secondary-text: #6c757d;
            --accent-color: #007bff;
            --success-color: #28a745;
            --hover-bg: #f1f3f5;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--primary-text);
            line-height: 1.5;
            padding: 20px;
            /* background-image: url('./dingdong.jpg');
            background-attachment: fixed;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat; */
        }
        
        header {
            margin-bottom: 30px;
            text-align: center;
            padding: 20px 0;
        }
        
        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }
        
        h1 {
            color: var(--accent-color);
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .judge-info {
            background-color: var(--secondary-bg);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .judge-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .category-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .category-card {
            background-color: var(--secondary-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .category-header {
            background-color: var(--accent-color);
            color: white;
            padding: 15px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .category-weight {
            background-color: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .category-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .category-description {
            color: var(--secondary-text);
            margin-bottom: 15px;
            font-size: 0.95rem;
            flex-grow: 1;
        }
        
        .progress-container {
            margin-top: auto;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--secondary-text);
            margin-bottom: 5px;
        }
        
        .progress-bar {
            height: 8px;
            background-color: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-value {
            height: 100%;
            background-color: var(--success-color);
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .btn {
            background-color: var(--accent-color);
            color: white;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: #0069d9;
        }
        
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: var(--success-color);
            color: white;
            font-family: 'Montserrat', sans-serif;
        }
        
        .status-badge.pending {
            background-color: #ffc107;
        }
        
        .status-badge.not-started {
            background-color: var(--secondary-text);
        }
        
        footer {
            margin-top: 40px;
            text-align: center;
            color: var(--secondary-text);
            font-size: 0.9rem;
            padding: 20px 0;
            border-top: 1px solid var(--border-color);
        }
        
        @media (max-width: 768px) {
            .category-list {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Hiyas System</h1>
            <p>Scoring Dashboard</p>
        </header>
        
        <div class="judge-info">
            <div class="judge-name">Judge: <span id="judgeName"></span></div>
            <!-- <div class="judge-id">ID: <span id="judgeId">J-2025-001</span></div> -->
        </div>
        
        <div class="dashboard-header">
            <h2>Scoring Categories</h2>
            <div>
                <button class="btn" onclick="showHelp()">Help</button>
            </div>
        </div>
        
        <div class="category-list">
            <!-- Production Number/Creative Cultural Attire -->
            <div class="category-card" onclick="navigateToCategory('cultural')">
                <div class="category-header">
                    <span>Cultural Attire</span>
                    <span class="category-weight">10%</span>
                </div>
                <!-- <div class="status-badge not-started">Not Started</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants celebrate cultural heritage through expressive and meaningful traditional attire with strong visual and cultural appeal.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="cultural-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="cultural-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Uniform Attire -->
            <div class="category-card" onclick="navigateToCategory('sports')">
                <div class="category-header">
                    <span>Sports Attire</span>
                    <span class="category-weight">10%</span>
                </div>
                <!-- <div class="status-badge pending">In Progress</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants present a confident and stylish look in sportswear, emphasizing uniqueness, appropriateness, and stage presence.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="sports-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="sports-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Advocacy Campaign Speech/Video -->
            <div class="category-card" onclick="navigateToCategory('advocacy')">
                <div class="category-header">
                    <span>Advocacy Campaign Speech/Video</span>
                    <span class="category-weight">15%</span>
                </div>
                <!-- <div class="status-badge not-started">Not Started</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants deliver a compelling speech highlighting their cause, structured effectively with clear articulation and strong relevance.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="advocacy-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="advocacy-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Casual Attire -->
            <div class="category-card" onclick="navigateToCategory('casual')">
                <div class="category-header">
                    <span>Casual Attire/Production Number</span>
                    <span class="category-weight">15%</span>
                </div>
                <!-- <div class="status-badge not-started">Not Started</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants showcase their stage presence, grace, and creativity through an energetic and well-executed performance.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="casual-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="casual-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Evening Gown and Formal Wear -->
            <div class="category-card" onclick="navigateToCategory('evening')">
                <div class="category-header">
                    <span>Evening Gown and Formal Wear</span>
                    <span class="category-weight">20%</span>
                </div>
                <!-- <div class="status-badge not-started">Not Started</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants display elegance, confidence, and poise in formal wear, highlighting their sophistication and presence.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="evening-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="evening-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Final Q&A -->
            <div class="category-card" onclick="navigateToCategory('qa')">
                <div class="category-header">
                    <span>Final Q and A</span>
                    <span class="category-weight">30%</span>
                </div>
                <!-- <div class="status-badge completed">Completed</div> -->
                <div class="category-body">
                    <div class="category-description">
                    Contestants respond to thought-provoking questions with clarity, fluency, and confidence, demonstrating intelligence and composure under pressure.
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span>Completion</span>
                            <span id="qa-progress">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-value" id="qa-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
    <p>© 2025 Hiyas System - Scoring Platform</p>
    <p>Created by <strong>Junior Programmer's Group</strong> in collaboration with the <strong>Commission on Audit of Notre Dame of Dadiangas University.</strong></p>
    </footer>
    
    <script>


    <?php

        function getScoringData($judgeName) {
            $judgeFile = "./src/judges-scores/" . $judgeName . ".json";
            $candidatesFile = "./src/candidates.json";
            
            if (!file_exists($judgeFile) || !file_exists($candidatesFile)) {
                die("Error: Missing required JSON files.");
            }
            
            // Read JSON files
            $judgeData = json_decode(file_get_contents($judgeFile), true);
            $candidatesData = json_decode(file_get_contents($candidatesFile), true);
            
            // Count total candidates
            $totalCandidates = count($candidatesData);
            
            // Default scoring structure
            $scoringData = [
                "cultural" => ["completed" => 0, "total" => $totalCandidates * 4],
                "sports" => ["completed" => 0, "total" => $totalCandidates * 4],
                "advocacy" => ["completed" => 0, "total" => $totalCandidates * 4],
                "casual" => ["completed" => 0, "total" => $totalCandidates * 4],
                "evening" => ["completed" => 0, "total" => $totalCandidates * 4],
                "qa" => ["completed" => 0, "total" => $totalCandidates * 4]
            ];
            
            // Compute completed scores
            foreach ($judgeData as $category => $scores) {
                if (isset($scoringData[$category])) {
                    //$scoringData[$category]["completed"] = count($scores);
                    $totalCatScores = 0;
                    foreach ($scores as $candidate => $criteria){
                        $totalCatScores += count($criteria);
                    }
                    $scoringData[$category]["completed"] = $totalCatScores;
                }
            }
            
            return json_encode($scoringData);
        }


        $scoringDataJson = getScoringData($judgeName);
    ?>



        // Simulated data - This would be replaced with actual data from localStorage or JSON files
        const scoringData = <?php echo $scoringDataJson; ?>;
        
        // Initialize the dashboard
        function initDashboard() {
            // Load judge information (could be from localStorage)
            // This is just placeholder data
            document.getElementById('judgeName').textContent = "<?php echo $judgeName; ?>";
            // document.getElementById('judgeId').textContent = 'J-2025-001';
            
            // Update progress bars
            updateProgressBars();
            
            // Set status badges
            updateStatusBadges();
        }
        
        // Update progress bars based on scoring data
        function updateProgressBars() {
            for (const category in scoringData) {
                const progress = (scoringData[category].completed / scoringData[category].total) * 100;
                document.getElementById(`${category}-progress`).textContent = `${Math.round(progress)}%`;
                document.getElementById(`${category}-bar`).style.width = `${progress}%`;
            }
        }
        
        // Update status badges based on progress
        function updateStatusBadges() {
            for (const category in scoringData) {
                const progress = (scoringData[category].completed / scoringData[category].total) * 100;
                const cards = document.querySelectorAll('.category-card');
                
                cards.forEach(card => {
                    if (card.onclick.toString().includes(category)) {
                        const badge = card.querySelector('.status-badge');
                        
                        if (progress === 0) {
                            badge.className = 'status-badge not-started';
                            badge.textContent = 'Not Started';
                        } else if (progress === 100) {
                            badge.className = 'status-badge completed';
                            badge.textContent = 'Completed';
                        } else {
                            badge.className = 'status-badge pending';
                            badge.textContent = 'In Progress';
                        }
                    }
                });
            }
        }
        
        // Navigate to category scoring page
        function navigateToCategory(categoryId) {
            window.location.href = `category.php?category=${categoryId}&code=<?php echo urlencode($targetCode);?>`;
        }
        
        // Show help modal
        function showHelp() {
            alert('Help for Hiyas System Scoring:\n\n1. Click on a category to begin scoring\n2. Each candidate must be scored for every category\n3. Complete all categories to finalize your judging');
        }
        
        // Initialize the dashboard when the page loads
        window.onload = initDashboard;
    </script>
</body>
</html>