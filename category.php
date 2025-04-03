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
            max-width: 1000px;
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

        .criteria-description {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }

        .criteria-description h3 {
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 0.5rem;
        }

        .criteria-description ul {
            margin-left: 1.5rem;
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
            width: 100px;
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

        .criteria-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.85rem;
        }

        .criteria-weight {
            font-size: 0.8rem;
            color: #6c757d;
            margin-left: 4px;
        }

        .criteria-section {
            margin-bottom: 6px;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .total-row td {
            border-top: 2px solid #007bff;
        }

        .criteria-group {
            border-left: 3px solid #e9ecef;
            padding-left: 10px;
            margin-bottom: 10px;
        }

        .accordion-btn {
            background: #f1f8ff;
            border: none;
            width: 100%;
            text-align: left;
            padding: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .candidate-details {
            display: none;
            padding: 15px;
            background: #fafafa;
            border-radius: 4px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .active-candidate {
            display: block;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="category-title"><?php
                // Dummy data for category titles with percentages
                $categoryTitles = [
                    "cultural" => "Cultural Attire (10%)",
                    "sports" => "Sports Attire (10%)",
                    "advocacy" => "Advocacy Campaign Speech/Video (15%)",
                    "casual" => "Casual Attire/Production Number (15%)",
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

        <div class="criteria-description">
            <h3>Scoring Criteria</h3>
            <div id="criteriaDescription">
                <!-- Criteria description will be populated based on category -->
            </div>
        </div>

        <div id="candidatesContainer">
            <!-- Candidate accordions will be generated here -->
        </div>

        <div class="button-group">
            <button class="btn btn-primary" onclick="returnDashboard()">Return to Dashboard</button>
            <button class="btn btn-success" onclick="submitScores()">Save All Scores</button>
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
                // If score format is now criteria-based
                $candidate['scores'] = $categoryScores[$candidateId] ?? []; 
            }

            // Encode final candidates data
            echo "const candidates = " . json_encode($candidatesArray, JSON_PRETTY_PRINT) . ";";
        ?>

        // Define criteria mapping for each category
        const criteriaMapping = {
            "cultural": [
                { id: "statement", name: "Statement/Appeal", weight: 40 },
                { id: "appreciation", name: "Cultural Appreciation", weight: 40 },
                { id: "stage", name: "Stage Presence", weight: 10 },
                { id: "overall", name: "Overall Impact", weight: 10 }
            ],
            "sports": [
                { id: "presence", name: "Presence and Confidence", weight: 40 },
                { id: "beauty", name: "Beauty (Uniqueness and Appropriateness)", weight: 30 },
                { id: "bearing", name: "Bearing of Attire", weight: 20 },
                { id: "overall", name: "Overall Impact", weight: 10 }
            ],
            "advocacy": [
                { id: "content", name: "Content/Topic/Speech Development", weight: 40 },
                { id: "fluency", name: "Fluency and Delivery", weight: 20 },
                { id: "relevance", name: "Relevance of the Campaign", weight: 30 },
                { id: "overall", name: "Overall Impact", weight: 10 }
            ],
            "casual": [
                { id: "poise", name: "Poise, Bearing and Stage Presence", weight: 40 },
                { id: "mastery", name: "Mastery and Gracefulness", weight: 30 },
                { id: "resourcefulness", name: "Resourcefulness (Props and Materials)", weight: 20 },
                { id: "overall", name: "Overall Impact", weight: 10 }
            ],
            "evening": [
                { id: "poise", name: "Poise and Bearing", weight: 40 },
                { id: "assertiveness", name: "Assertiveness", weight: 30 },
                { id: "elegance", name: "Elegance", weight: 20 },
                { id: "overall", name: "Overall Impact", weight: 10 }
            ],
            "qa": [
                { id: "content", name: "Content/Substance/Relevance", weight: 40 },
                { id: "fluency", name: "Fluency", weight: 20 },
                { id: "language", name: "Mastery of Language (Speech, Vocabulary, Grammar)", weight: 30 },
                { id: "personality", name: "Personality and Emotional Control", weight: 10 }
            ]
        };

        // Get current category
        const currentCategory = '<?php echo $categoryId; ?>';
        const criteria = criteriaMapping[currentCategory] || [];

        document.addEventListener('DOMContentLoaded', () => {
            populateCriteriaDescription();
            renderCandidates();
        });

        function populateCriteriaDescription() {
            const container = document.getElementById('criteriaDescription');
            if (!criteria.length) {
                container.innerHTML = '<p>No specific criteria defined for this category.</p>';
                return;
            }

            let html = '<ul>';
            criteria.forEach(c => {
                html += `<li><strong>${c.name}</strong> - ${c.weight}%</li>`;
            });
            html += '</ul>';
            
            container.innerHTML = html;
        }

        function renderCandidates() {
            const container = document.getElementById('candidatesContainer');
            
            const candidatesHtml = candidates.map((candidate, index) => {
                // Generate criteria inputs for this candidate
                const criteriaInputs = criteria.map(c => {
                    const savedValue = candidate.scores[c.id] || '';
                    return `
                        <div class="criteria-section">
                            <div class="criteria-label">
                                ${c.name} <span class="criteria-weight">(${c.weight}%)</span>
                            </div>
                            <input type="number" 
                                   class="score-input" 
                                   data-criteria="${c.id}"
                                   data-max="${c.weight}"
                                   min="0" 
                                   max="${c.weight}" 
                                   step="0.1" 
                                   value="${savedValue}"
                                   placeholder="0 - ${c.weight}"
                                   oninput="validateCriteriaScore(this)"
                                   onblur="calculateTotal('${candidate.id}'); autoSaveScore(this, '${candidate.id}')">
                        </div>
                    `;
                }).join('');
                
                // Calculate the total based on saved values
                let total = 0;
                criteria.forEach(c => {
                    const score = parseFloat(candidate.scores[c.id] || 0);
                    if (!isNaN(score)) {
                        total += score;
                    }
                });
                
                return `
                    <div class="candidate-container" data-candidate-id="${candidate.id}">
                        <button class="accordion-btn" onclick="toggleCandidate('${candidate.id}')">
                            <div class="candidate-info">
                                <div class="candidate-number">${candidate.number}</div>
                                <div>${candidate.name}</div>
                            </div>
                            <div>Total: <span id="total-${candidate.id}">${total.toFixed(1)}</span>/100.0</div>
                        </button>
                        
                        <div class="candidate-details" id="details-${candidate.id}">
                            <div class="criteria-group">
                                ${criteriaInputs}
                            </div>
                            <div style="text-align: right; font-weight: 600; margin-top: 10px;">
                                Total Score: <span id="total-display-${candidate.id}">${total.toFixed(1)}</span>/100.0
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = candidatesHtml;
            
            // Open the first candidate by default if any exist
            if (candidates.length > 0) {
                toggleCandidate(candidates[0].id);
            }
        }

        function toggleCandidate(candidateId) {
            const detailsElement = document.getElementById(`details-${candidateId}`);
            const allDetails = document.querySelectorAll('.candidate-details');
            
            // Close all other details
            allDetails.forEach(el => {
                if (el.id !== `details-${candidateId}`) {
                    el.classList.remove('active-candidate');
                }
            });
            
            // Toggle this candidate's details
            detailsElement.classList.toggle('active-candidate');
        }

        function validateCriteriaScore(input) {
            const value = parseFloat(input.value);
            const max = parseFloat(input.dataset.max);
            
            input.classList.toggle('invalid', isNaN(value) || value < 0 || value > max);
        }

        function calculateTotal(candidateId) {
            const candidateElement = document.querySelector(`[data-candidate-id="${candidateId}"]`);
            let total = 0;
            
            candidateElement.querySelectorAll('.score-input').forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value) && value >= 0) {
                    total += value;
                }
            });
            
            // Update total display
            document.getElementById(`total-${candidateId}`).textContent = total.toFixed(1);
            document.getElementById(`total-display-${candidateId}`).textContent = total.toFixed(1);
        }

        function submitScores() {
            let isValid = true;
            const allScores = {};

            // Validate and collect all scores
            candidates.forEach(candidate => {
                const candidateElement = document.querySelector(`[data-candidate-id="${candidate.id}"]`);
                const candidateScores = {};
                
                candidateElement.querySelectorAll('.score-input').forEach(input => {
                    const criteriaId = input.dataset.criteria;
                    const max = parseFloat(input.dataset.max);
                    const value = parseFloat(input.value);
                    
                    if (isNaN(value) || value < 0 || value > max) {
                        input.classList.add('invalid');
                        isValid = false;
                    } else {
                        candidateScores[criteriaId] = value;
                    }
                });
                
                allScores[candidate.id] = candidateScores;
            });

            if (!isValid) {
                showStatus('Please fix invalid scores before submitting', '#dc3545');
                return;
            }
            else{
                showStatus('All scores submitted successfully!', '#28a745');
            }

            // Submit all scores at once
            // const formData = new FormData();
            // formData.append('judge_name', '<?php echo $judgeName?>');
            // formData.append('category', '<?php echo $categoryId?>');
            // formData.append('scores', JSON.stringify(allScores));

            // fetch('save_scores.php', {
            //     method: 'POST',
            //     body: formData
            // })
            // .then(response => response.json())
            // .then(data => {
            //     if (data.success) {
            //         showStatus('All scores submitted successfully!', '#28a745');
            //     } else {
            //         showStatus('Failed to save scores: ' + (data.message || 'Unknown error'), '#dc3545');
            //     }
            // })
            // .catch(error => {
            //     console.error('Error:', error);
            //     showStatus('An error occurred while saving scores', '#dc3545');
            // });
        }

        function autoSaveScore(input, candidateId) {
            const value = parseFloat(input.value);
            const criteriaId = input.dataset.criteria;
            const max = parseFloat(input.dataset.max);
            
            if (isNaN(value) || value < 0 || value > max) return; // Prevent invalid scores

            // Prepare data to send
            const formData = new FormData();
            formData.append('candidate_id', candidateId);
            formData.append('criteria_id', criteriaId);
            formData.append('score', value);
            formData.append('judge_name', '<?php echo $judgeName?>');
            formData.append('category', '<?php echo $categoryId?>');

            // Send AJAX request to save score
            fetch('save_criteria_score.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Score saved!', '#28a745');
                } else {
                    showStatus('Failed to save score.', '#dc3545');
                }
            })
            .catch(error => console.error('Error:', error));
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
    </script>
</body>
</html>