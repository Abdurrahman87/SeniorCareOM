<?php
/**
 * Process Volunteer Registration Form
 * 
 * This script handles incoming data from the volunteer registration form
 * and interacts with the MySQL database to store the volunteer information.
 */

// Database connection parameters
$servername = "localhost";
$username = "root";  // Replace with your actual database username
$password = "";      // Replace with your actual database password
$dbname = "website_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process only if the form has been submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $name = htmlspecialchars($_POST["volunteerName"]);
    $email = filter_var($_POST["volunteerEmail"], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST["volunteerPhone"]);
    $availability = htmlspecialchars($_POST["volunteerAvailability"]);
    
    // Validate phone number (8 digits)
    if (!preg_match("/^\d{8}$/", $phone)) {
        echo displayError("Invalid phone number. Please enter an 8-digit phone number.");
        exit();
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo displayError("Invalid email format. Please enter a valid email address.");
        exit();
    }
    
    // Create a SQL query to insert the volunteer
    $sql = "INSERT INTO users (name, user_type, email) VALUES (?, 'volunteer', ?)";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo displayError("Error preparing statement: " . $conn->error);
        exit();
    }
    
    // Bind parameters and execute
    $stmt->bind_param("ss", $name, $email);
    
    if ($stmt->execute()) {
        // Get the last inserted ID (volunteer's user_id)
        $user_id = $conn->insert_id;
        
        // Additional metadata could be stored in a volunteers table
        // For example, storing availability in a separate table
        $sql2 = "INSERT INTO volunteer_availability (user_id, availability, phone) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        
        if ($stmt2) {
            $stmt2->bind_param("iss", $user_id, $availability, $phone);
            $stmt2->execute();
            $stmt2->close();
        }
        
        // Display success message
        echo displaySuccess("Volunteer registration successful!", $name, $email, $phone, $availability);
    } else {
        echo displayError("Error: " . $stmt->error);
    }
    
    // Close statement
    $stmt->close();
}

$conn->close();

/**
 * Function to display a formatted error message
 * 
 * @param string $message The error message to display
 * @return string HTML formatted error message
 */
function displayError($message) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Error</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Registration Error</h4>
                <p>' . $message . '</p>
                <hr>
                <p class="mb-0">
                    <a href="volunteers_and_organizations.html" class="btn btn-primary">Back to Registration Form</a>
                </p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

/**
 * Function to display a formatted success message
 * 
 * @param string $message Success message
 * @param string $name Volunteer name
 * @param string $email Volunteer email
 * @param string $phone Volunteer phone
 * @param string $availability Volunteer availability
 * @return string HTML formatted success message with volunteer details
 */
function displaySuccess($message, $name, $email, $phone, $availability) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Successful</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">' . $message . '</h4>
                <hr>
                <table class="table">
                    <tr>
                        <th>Name:</th>
                        <td>' . $name . '</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>' . $email . '</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>' . $phone . '</td>
                    </tr>
                    <tr>
                        <th>Availability:</th>
                        <td>' . $availability . '</td>
                    </tr>
                </table>
                <p class="mb-0">
                    <a href="index.html" class="btn btn-primary">Return to Homepage</a>
                </p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>