<?php include "authorizer.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judging Portal - Category Scoring</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Inter:wght@400;500&family=Montserrat:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            padding: 1rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-top: 100px;
        }

        .header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #007bff;
        }

        .category-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            color: #212529;
            margin-bottom: 0.25rem;
        }

        .scoring-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .scoring-table th {
            background-color: #007bff;
            color: white;
            padding: 0.75rem;
            text-align: left;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
        }

        .scoring-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        .candidate-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .candidate-number {
            background: #007bff;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
        }

        .score-input {
            width: 120px;
            padding: 0.5rem;
            border: 2px solid #007bff;
            border-radius: 4px;
            font-size: 0.9rem;
            text-align: center;
            transition: all 0.2s;
        }

        .score-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .score-input.invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .button-group {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.25rem;
            border: none;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .status-message {
            text-align: center;
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="category-title"><?php
                $categoryTitles = [
                    "production" => "Production Number (10%)",
                    "uniform" => "Uniform Attire (10%)",
                    "advocacy" => "Advocacy Campaign (15%)",
                    "casual" => "Casual Attire (15%)",
                    "evening" => "Evening Gown/Formal (20%)",
                    "qa" => "Final Q&A (30%)",
                ];
                
                // Get category parameter from URL
                $categoryId = isset($_GET['category']) ? $_GET['category'] : '';
                
                // Determine the category title (default to "Unknown Category" if not found)
                $categoryTitle = isset($categoryTitles[$categoryId]) ? $categoryTitles[$categoryId] : "Unknown Category";

                echo htmlspecialchars($categoryTitle);
            ?></h1>
        </div>

        <table class="scoring-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Candidate</th>
                    <th style="width: 40%;">Score (0.0 - 10.0)</th>
                </tr>
            </thead>
            <tbody id="candidatesContainer">
                <!-- Candidate rows will be generated here -->
            </tbody>
        </table>

        <div class="button-group">
            <button class="btn btn-primary" onclick="returnDashboard()">Return to Dashboard</button>
            <button class="btn btn-success" onclick="submitScores()">Save</button>
        </div>
        <div class="status-message" id="statusMessage"></div>
    </div>

    <script>
        // Dummy data

        <?php
        // Load candidates data
            $candidatesJson = file_get_contents('./src/candidates.json'); 
            $candidatesArray = json_decode($candidatesJson, true);

            // Load scores file (assuming judge-specific JSON exists)
            $scoresFile = "./src/judges-scores/{$judgeName}.json";
            $scoresArray = file_exists($scoresFile) ? json_decode(file_get_contents($scoresFile), true) : [];

            // Get category scores (assuming category is in GET)
            $categoryScores = $scoresArray[$categoryId] ?? [];

            // Merge scores into candidates
            foreach ($candidatesArray as &$candidate) {
                $candidateId = $candidate['id'];
                $candidate['score'] = $categoryScores[$candidateId] ?? ''; // Assign score if exists, otherwise empty
            }

            // Encode final candidates data
            echo "const candidates = " . json_encode($candidatesArray, JSON_PRETTY_PRINT) . ";";
        ?>

        document.addEventListener('DOMContentLoaded', () => {
            renderCandidates();
            loadSavedData();
        });

        function renderCandidates() {
            const container = document.getElementById('candidatesContainer');
            container.innerHTML = candidates.map(candidate => `
                <tr data-candidate-id="${candidate.id}">
                    <td>
                        <div class="candidate-info">
                            <div class="candidate-number">${candidate.number}</div>
                            <div>
                                <div style="font-weight: 500;">${candidate.name}</div>
                                
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="score-input" 
                               min="0" max="10" step="0.1" 
                               placeholder="Enter score"
                               value="${candidate.score}"
                               oninput="validateScore(this)"
                               onblur="autoSaveScore(this)">
                    </td>
                </tr>
            `).join('');
        }

        function validateScore(input) {
            const value = parseFloat(input.value);
            input.classList.toggle('invalid', isNaN(value) || value < 0 || value > 10);
        }

        function saveProgress() {
            const scores = {};
            let hasErrors = false;
            
            document.querySelectorAll('.score-input').forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value) && (value < 0 || value > 10)) {
                    input.classList.add('invalid');
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                showStatus('Please fix invalid scores before saving', '#dc3545');
                return;
            }

            const data = {};
            document.querySelectorAll('tr[data-candidate-id]').forEach(row => {
                const id = row.dataset.candidateId;
                data[id] = row.querySelector('.score-input').value;
            });
            
            localStorage.setItem('savedScores', JSON.stringify(data));
            showStatus('Draft saved successfully', '#28a745');
        }

        function loadSavedData() {
            const savedData = localStorage.getItem('savedScores');
            if (savedData) {
                const scores = JSON.parse(savedData);
                Object.entries(scores).forEach(([id, score]) => {
                    const row = document.querySelector(`tr[data-candidate-id="${id}"]`);
                    if (row) {
                        const input = row.querySelector('.score-input');
                        input.value = score;
                        validateScore(input);
                    }
                });
            }
        }

        function submitScores() {
            let isValid = true;
            const scores = {};

            document.querySelectorAll('.score-input').forEach(input => {
                const value = parseFloat(input.value);
                if (isNaN(value) || value < 0 || value > 10) {
                    input.classList.add('invalid');
                    isValid = false;
                }
                scores[input.closest('tr').dataset.candidateId] = value;
            });

            if (!isValid) {
                showStatus('Please fix invalid scores before submitting', '#dc3545');
                return;
            }
            showStatus('Scores submitted successfully!', '#28a745');
            localStorage.removeItem('savedScores');
        }

        function showStatus(message, color) {
            const status = document.getElementById('statusMessage');
            status.textContent = message;
            status.style.backgroundColor = color + '15';
            status.style.color = color;
            setTimeout(() => {
                status.textContent = '';
                status.style.backgroundColor = '';
            }, 3000);
        }
        function returnDashboard() {
            window.location.href = `dashboard.php?code=<?php echo urlencode($targetCode);?>`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.score-input').forEach(input => {
                input.dataset.previousValue = input.value; // Store initial value
            });
        });

        function autoSaveScore(input) {
            const value = parseFloat(input.value);
            if (isNaN(value) || value < 0 || value > 10) return; // Prevent invalid scores

            // Check if the value actually changed
            if (input.dataset.previousValue === input.value) {
                return; // No change, skip saving
            }

            const candidateId = input.closest('tr').dataset.candidateId;

            // Prepare data to send
            const formData = new FormData();
            formData.append('candidate_id', candidateId);
            formData.append('score', value);
            formData.append('judge_name', '<?php echo $judgeName?>');
            formData.append('category', '<?php echo $categoryId?>');

            // Send AJAX request to save score
            fetch('save_score.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Score saved!', '#28a745');
                    input.dataset.previousValue = input.value; // Update stored value
                } else {
                    showStatus('Failed to save score.', '#dc3545');
                }
            })
            .catch(error => console.error('Error:', error));
        }



    </script>
</body>
</html>