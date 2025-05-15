<?php
// Database connection settings
$host = "localhost";
$dbname = "seniorcare";
$username = "db_username";
$password = "db_password";

// Start session
session_start();

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables to store form data
$name = $email = $user_type = $gender = "";
$errors = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = sanitize_input($_POST["name"]);
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $user_type = sanitize_input($_POST["user_type"]);
    $gender = isset($_POST["gender"]) ? sanitize_input($_POST["gender"]) : "";
    $terms = isset($_POST["terms"]) ? true : false;
    
    // Validation
    
    // Name validation
    if (empty($name)) {
        $errors[] = "Full name is required";
    }
    
    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    // Confirm password validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // User type validation
    if (empty($user_type)) {
        $errors[] = "User type is required";
    }
    
    // Terms validation
    if (!$terms) {
        $errors[] = "You must agree to the terms and conditions";
    }
    
    // If no validation errors, proceed with database connection
    if (empty($errors)) {
        try {
            // Create database connection
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            // Set PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already exists. Please use a different email or sign in.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Prepare SQL statement to insert user data
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type, gender, created_at) 
                                        VALUES (:name, :email, :password, :user_type, :gender, NOW())");
                
                // Bind parameters
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $hashed_password);
                $stmt->bindParam(":user_type", $user_type);
                $stmt->bindParam(":gender", $gender);
                
                // Execute statement
                $stmt->execute();
                
                // Get the newly created user ID
                $user_id = $conn->lastInsertId();
                
                // Store user info in session
                $_SESSION["user_id"] = $user_id;
                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $email;
                $_SESSION["user_type"] = $user_type;
                $_SESSION["logged_in"] = true;
                
                // Redirect to forum page
                header("Location: forum_page.html");
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
        
        $conn = null; // Close connection
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Result - Senior Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center">Sign Up Result</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h4>Please correct the following errors:</h4>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="text-center mt-3">
                                <a href="forum_page.html" class="btn btn-primary">Try Again</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Please submit the form to sign up.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
