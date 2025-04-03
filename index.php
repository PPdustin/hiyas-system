<?php
// Start a session to maintain login state


// Function to check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $accessCode = $_POST["accessCode"];
    $userType = $_POST["userType"];
    
    // Validate for Judge user type
    if ($userType === "judge") {
        // Path to the JSON file
        $jsonFilePath = './src/judges.json';
        
        // Check if the file exists
        if (file_exists($jsonFilePath)) {
            // Read the JSON file
            $jsonData = file_get_contents($jsonFilePath);
            
            // Decode the JSON data
            $judges = json_decode($jsonData, true);
            
            // Flag to track if a match is found
            $matchFound = false;
            
            // Loop through the judges data to find a matching code
            foreach ($judges as $judge) {
                if ($judge['code'] === $accessCode) {
                    // Match found, set the flag to true
                    $matchFound = true;
                    
                    // Store judge information in session
                    
                    // Redirect to the dashboard page with the code parameter
                    header("Location: dashboard.php?code=" . urlencode($accessCode));
                    exit();
                }
            }
            
            // If no match was found, set an error message
            if (!$matchFound) {
                $errorMessage = "Invalid access code. Please try again.";
            }
        } else {
            // If the JSON file doesn't exist, set an error message
            $errorMessage = "System error: Judges data not found. Please contact administrator.";
        }
    } elseif ($userType === "admin") {
        // For admin type users, you would implement a similar validation process
        // using an admin-specific JSON file or database
        // This is just a placeholder for now
        if($accessCode === 'jpg2025'){
            header("Location: admin.php?code=" . urlencode($accessCode));
            exit();
        }
        else{
            $errorMessage = "Invalid access code. Please try again.";
        }
    } else {
        // If user type is invalid, set an error message
        $errorMessage = "Invalid user type selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Code Access</title>
    <!-- Google Fonts -->
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
            --border-color: #dee2e6;
            --hover-bg: #e9ecef;
            --error-color: #dc3545;
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background-color: var(--secondary-bg);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
            color: var(--primary-text);
            text-align: center;
        }
        
        .subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            color: var(--secondary-text);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            font-size: 14px;
            display: block;
            margin-bottom: 8px;
            color: var(--primary-text);
        }
        
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--secondary-bg);
            color: var(--primary-text);
            transition: border-color 0.2s;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin: 12px 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        
        .radio-option:hover {
            background-color: var(--hover-bg);
        }
        
        .radio-option input {
            margin-right: 8px;
            accent-color: var(--accent-color);
            cursor: pointer;
        }
        
        .radio-option span {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }
        
        button {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            width: 100%;
            padding: 14px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        button:hover {
            background-color: #0069d9;
        }
        
        .form-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--secondary-text);
        }
        
        .error-message {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 5px;
            padding: 8px;
            background-color: #f8d7da;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
        }
        
        /* Animation for emphasis */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .login-container {
            animation: pulse 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Access Portal</h1>
        <p class="subtitle">Enter your code to login</p>
        
        <?php if (isset($errorMessage)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="accessCode">Access Code</label>
                <input type="password" id="accessCode" name="accessCode" placeholder="Enter your access code" required>
            </div>
            
            <div class="form-group">
                <label>User Type</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="userType" value="judge" checked>
                        <span>Judge</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="userType" value="admin">
                        <span>Admin</span>
                    </label>
                </div>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="form-footer">
            <!-- If you don't have an access code, please contact your administrator -->
             <?= "Address: " . gethostbyname(gethostname()); ?>
        </div>
    </div>
</body>
</html>