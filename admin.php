<?php
    include "functions.php";

    $code = $_GET['code'] ?? "";
    if ($code != 'jpg2025') {
        ?>
            <h1>YOU ARE NOT AUTHORIZED</h1>
        <?php
            exit; // STOP execution here if unauthorized
        }
        ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&family=Montserrat:wght@500;600&display=swap" rel="stylesheet">
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #212529;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        /* Header */
        header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #007bff;
        }
        
        /* Navigation */
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            font-family: 'Montserrat', sans-serif;
            text-decoration: none;
            color: #6c757d;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover, nav ul li a.active {
            color: #007bff;
        }
        
        /* Tabs */
        .tab-container {
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
        }
        
        .tab {
            font-family: 'Montserrat', sans-serif;
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }
        
        .tab-content {
            display: none;
            padding: 20px 0;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
        }
        
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }
        
        .btn {
            font-family: 'Montserrat', sans-serif;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-results{
            font-family: 'Montserrat', sans-serif;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(100, 108, 255, 0.3);
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        th {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        /* Alerts */
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #ffffff;
            border-radius: 8px;
            max-width: 500px;
            margin: 10% auto;
            padding: 20px;
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Small utility classes */
        .mt-20 {
            margin-top: 20px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }

        .resultsContainer{
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Admin Dashboard</div>
                <nav>
                    <ul>
                        <li><a href="index.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" data-tab="candidates">Manage Candidates</div>
                    <div class="tab" data-tab="judges">Manage Judges</div>

                </div>

                <!-- Candidate Tab Content -->
                <div class="tab-content active" id="candidates">
                    <div class="alert alert-success" id="candidate-alert">Candidate updated successfully!</div>
                    
                    <div class="text-right">
                        <button class="btn btn-primary" id="add-candidate-btn">Add New Candidate</button>
                    </div>
                    
                    <table class="mt-20">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Name</th>
                                <th>Pre-Pageant</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="candidates-table-body">
                            <!-- Filled via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Judges Tab Content -->
                <div class="tab-content" id="judges">
                    <div class="alert alert-success" id="judge-alert">Judge added successfully!</div>
                    
                    <div class="text-right">
                        <button class="btn btn-primary" id="add-judge-btn">Add New Judge</button>
                    </div>
                    
                    <table class="mt-20">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Access Code</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="judges-table-body">
                            <!-- Filled via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidate Modal -->
    <div class="modal" id="candidate-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="candidate-modal-title">Add New Candidate</h2>
            
            <form id="candidate-form">
                <input type="hidden" id="candidate-id">
                
                <div class="form-group">
                    <label for="candidate-number">Candidate Number</label>
                    <input type="number" id="candidate-number" required>
                </div>
                
                <div class="form-group">
                    <label for="candidate-name">Candidate Name</label>
                    <input type="text" id="candidate-name" required>
                </div>

                <div class="form-group">
                    <label for="candidate-prepageant">Pre-pageant Score</label>
                    <input type="number" id="candidate-prepageant" required>
                </div>
                
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Save Candidate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Judge Modal -->
    <div class="modal" id="judge-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="judge-modal-title">Add New Judge</h2>
            
            <form id="judge-form">
                <input type="hidden" id="judge-id">
                
                <div class="form-group">
                    <label for="judge-name">Judge Name</label>
                    <input type="text" id="judge-name" required>
                </div>
                
                <div class="form-group">
                    <label for="judge-code">Access Code</label>
                    <input type="text" id="judge-code" required>
                </div>
                
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Save Judge</button>
                </div>
            </form>
        </div>
    </div>

    <div class="resultsContainer">
    <button class="btn-primary btn-results" id="scores" onclick="viewResults()">View Scores</button>
    </div>

    <script>
        // Dummy data
        // let candidates = [
        //     { id: 1, number: 1, name: "Anna Johnson" },
        //     { id: 2, number: 2, name: "Michael Smith" },
        //     { id: 3, number: 3, name: "Sarah Williams" }
        // ];
        
        // let judges = [
        //     { id: 1, name: "Dr. James Wilson", code: "JUDGE001" },
        //     { id: 2, name: "Prof. Emma Davis", code: "JUDGE002" }
        // ];
        <?php
            $candidatesJson = file_get_contents('./src/candidates.json'); // Read JSON file
            $candidates = json_decode($candidatesJson, true); // Convert JSON to PHP array
            echo "let candidates = " . json_encode($candidates, JSON_PRETTY_PRINT) . ";";
        ?>

        <?php
            $judgesJson = file_get_contents('./src/judges.json');
            $judges = json_decode($judgesJson, true);
            
            // Calculate progress for each judge
            foreach ($judges as &$judge) {
                // Add progress directly to each judge object
                $judge['progress'] = calculateJudgeProgress($judge['name'], $candidates);
            }
            
            // Output the enhanced judges array as JavaScript
            echo "let judges = " . json_encode($judges, JSON_PRETTY_PRINT) . ";";
        ?>
        
        // DOM Elements
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        const candidateModal = document.getElementById('candidate-modal');
        const judgeModal = document.getElementById('judge-modal');
        const closeButtons = document.querySelectorAll('.close');
        
        const candidateForm = document.getElementById('candidate-form');
        const judgeForm = document.getElementById('judge-form');
        
        const candidateAlert = document.getElementById('candidate-alert');
        const judgeAlert = document.getElementById('judge-alert');
        
        // Tab switching
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Update active content
                tabContents.forEach(content => content.classList.remove('active'));
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Modal management
        document.getElementById('add-candidate-btn').addEventListener('click', () => {
            document.getElementById('candidate-modal-title').textContent = 'Add New Candidate';
            document.getElementById('candidate-id').value = '';
            document.getElementById('candidate-form').reset();
            candidateModal.style.display = 'block';
        });
        
        document.getElementById('add-judge-btn').addEventListener('click', () => {
            document.getElementById('judge-modal-title').textContent = 'Add New Judge';
            document.getElementById('judge-id').value = '';
            document.getElementById('judge-form').reset();
            judgeModal.style.display = 'block';
        });
        
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                candidateModal.style.display = 'none';
                judgeModal.style.display = 'none';
            });
        });
        
        window.addEventListener('click', (event) => {
            if (event.target === candidateModal) candidateModal.style.display = 'none';
            if (event.target === judgeModal) judgeModal.style.display = 'none';
        });
        
        // Form submissions
        candidateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('candidate-id').value;
            const number = document.getElementById('candidate-number').value;
            const name = document.getElementById('candidate-name').value;
            const prepageant = document.getElementById('candidate-prepageant').value
            
            if (id) {
                // Update existing candidate
                const index = candidates.findIndex(c => c.id == id);
                candidates[index] = { id: parseInt(id), number, name, prepageant };

                const formData = new FormData();
                formData.append('id', id);
                formData.append('name', name);
                formData.append('number', number);
                formData.append('prepageant', prepageant);

                fetch('update-candidate.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.reset();
                })
                .catch(error => console.error('Error:', error));

            } else {
                // Add new candidate
                const newId = candidates.length > 0 ? Math.max(...candidates.map(c => c.id)) + 1 : 1;
                candidates.push({ id: newId, number, name, prepageant });

                const formData = new FormData();
                formData.append('id', newId);
                formData.append('name', name);
                formData.append('number', number);
                formData.append('prepageant', prepageant);

                fetch('save-candidate.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.reset();
                })
                .catch(error => console.error('Error:', error));
            }
            
            renderCandidates();
            candidateModal.style.display = 'none';
            
            // Show success alert
            candidateAlert.style.display = 'block';
            setTimeout(() => {
                candidateAlert.style.display = 'none';
            }, 3000);
        });
        
        judgeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('judge-id').value;
            const name = document.getElementById('judge-name').value;
            const code = document.getElementById('judge-code').value;

            
            
            if (id) {
                // Update existing judge
                const index = judges.findIndex(j => j.id == id);
                judges[index] = { id: parseInt(id), name, code };

                const formData = new FormData();
                formData.append('id', id);
                formData.append('name', name);
                formData.append('code', code);

                fetch('update-judge.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.reset();
                })
                .catch(error => console.error('Error:', error));
                
            } else {
                // Add new judge
                const newId = judges.length > 0 ? Math.max(...judges.map(j => j.id)) + 1 : 1;
                judges.push({ id: newId, name, code });

                const formData = new FormData();
                formData.append('id', newId);
                formData.append('name', name);
                formData.append('code', code);

                fetch('save-judge.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.reset();
                })
                .catch(error => console.error('Error:', error));
                
                
            }
            renderJudges();
            judgeModal.style.display = 'none';
            
            // Show success alert
            judgeAlert.style.display = 'block';
            setTimeout(() => {
                judgeAlert.style.display = 'none';
            }, 3000);

            
        });
        
        // Render functions
        function renderCandidates() {
            const tableBody = document.getElementById('candidates-table-body');
            tableBody.innerHTML = '';
            
            candidates.forEach(candidate => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${candidate.number}</td>
                    <td>${candidate.name}</td>
                    <td>${candidate.prepageant}</td>
                    <td class="action-btns">
                        <button class="btn btn-primary edit-candidate" data-id="${candidate.id}">Edit</button>
                        <button class="btn btn-danger delete-candidate" data-id="${candidate.id}">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Add event listeners for edit/delete buttons
            addCandidateEventListeners();
        }
        
        function renderJudges() {
            const tableBody = document.getElementById('judges-table-body');
            tableBody.innerHTML = '';
            
            judges.forEach(judge => {
                let progressColor;
                if (judge.progress >= 100) {
                    progressColor = '#006d32'; // Deep forest green (AAA contrast)
                } else {
                    if (judge.progress >= 75) {
                        progressColor = '#ffab00'; // Rich terracotta (AA+ contrast)
                    } else if (judge.progress >= 50) {
                        progressColor = '#ffab00'; // Dark safety orange
                    } else {
                        progressColor = '#ffab00'; // Deep golden yellow
                    }
                }


                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${judge.name}</td>
                    <td>${judge.code}</td>
                    <td><span style="color: ${progressColor}; font-weight: bold;">${judge.progress}%</span></td>
                    <td class="action-btns">
                        <button class="btn btn-primary edit-judge" data-id="${judge.id}">Edit</button>
                        <button class="btn btn-danger delete-judge" data-id="${judge.id}">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Add event listeners for edit/delete buttons
            addJudgeEventListeners();
        }
        
        function addCandidateEventListeners() {
            document.querySelectorAll('.edit-candidate').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const candidate = candidates.find(c => c.id == id);
                    
                    document.getElementById('candidate-modal-title').textContent = 'Edit Candidate';
                    document.getElementById('candidate-id').value = candidate.id;
                    document.getElementById('candidate-number').value = candidate.number;
                    document.getElementById('candidate-name').value = candidate.name;
                    document.getElementById('candidate-prepageant').value = candidate.prepageant;
                    
                    candidateModal.style.display = 'block';
                });
            });
            
            document.querySelectorAll('.delete-candidate').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this candidate?')) {
                        const id = this.getAttribute('data-id');
                        candidates = candidates.filter(c => c.id != id);

                        const formData = new FormData();
                        formData.append('id', id);


                        fetch('delete-candidate.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            this.reset();
                        })
                        .catch(error => console.error('Error:', error));

                        renderCandidates();
                        
                        // Show success alert
                        candidateAlert.textContent = 'Candidate deleted successfully!';
                        candidateAlert.style.display = 'block';
                        setTimeout(() => {
                            candidateAlert.style.display = 'none';
                        }, 3000);
                    }
                });
            });
        }
        
        function addJudgeEventListeners() {
            document.querySelectorAll('.edit-judge').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const judge = judges.find(j => j.id == id);
                    
                    document.getElementById('judge-modal-title').textContent = 'Edit Judge';
                    document.getElementById('judge-id').value = judge.id;
                    document.getElementById('judge-name').value = judge.name;
                    document.getElementById('judge-code').value = judge.code;
                    
                    judgeModal.style.display = 'block';
                });
            });
            
            document.querySelectorAll('.delete-judge').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this judge?')) {
                        const id = this.getAttribute('data-id');
                        judges = judges.filter(j => j.id != id);


                        const formData = new FormData();
                        formData.append('id', id);


                        fetch('delete-judge.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            this.reset();
                        })
                        .catch(error => console.error('Error:', error));


                        renderJudges();
                        
                        // Show success alert
                        judgeAlert.textContent = 'Judge deleted successfully!';
                        judgeAlert.style.display = 'block';
                        setTimeout(() => {
                            judgeAlert.style.display = 'none';
                        }, 3000);
                    }
                });
            });
        }

        function viewResults(){
            window.location.href = `xresults.php?code=<?php echo urlencode($code);?>`;
        }

        
        
        // Initial render
        renderCandidates();
        renderJudges();
    </script>
</body>
</html>