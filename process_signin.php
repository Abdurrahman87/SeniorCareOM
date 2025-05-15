<?php
// Database connection settings
$host = "localhost";
$dbname = "seniorcare";
$username = "db_username";
$password = "db_password";

// Start session for user authentication
session_start();

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"]; // Password will be verified with hash
    $remember = isset($_POST["remember-me"]) ? true : false;
    
    // Validation
    $errors = [];
    
    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no validation errors, proceed with database connection
    if (empty($errors)) {
        try {
            // Create database connection
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            // Set PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Prepare SQL statement to check user credentials
            $stmt = $conn->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            // Check if user exists
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user["password"])) {
                    // Password is correct, store user info in session
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["name"];
                    $_SESSION["user_email"] = $user["email"];
                    $_SESSION["user_type"] = $user["user_type"];
                    $_SESSION["logged_in"] = true;
                    
                    // If remember me was checked, set cookies
                    if ($remember) {
                        $token = bin2hex(random_bytes(16));
                        $expiry = time() + (86400 * 30); // 30 days
                        
                        // Store token in database
                        $stmt = $conn->prepare("UPDATE users SET remember_token = :token, token_expiry = :expiry WHERE id = :id");
                        $stmt->bindParam(":token", $token);
                        $stmt->bindParam(":expiry", $expiry);
                        $stmt->bindParam(":id", $user["id"]);
                        $stmt->execute();
                        
                        // Set cookies
                        setcookie("remember_user", $user["id"], $expiry, "/");
                        setcookie("remember_token", $token, $expiry, "/");
                    }
                    
                    // Redirect to forum page
                    header("Location: forum_page.html");
                    exit();
                } else {
                    $errors[] = "Invalid password";
                }
            } else {
                $errors[] = "User not found";
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
    <title>Sign In Result - Senior Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center">Sign In Result</h2>
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
                                <p>Please submit the form to sign in.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
