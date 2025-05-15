<?php
/**
 * Process Organization Registration Form
 * 
 * This script handles incoming data from the organization registration form
 * and interacts with the MySQL database to store the organization information.
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
    $orgName = htmlspecialchars($_POST["organization_name"]);
    $contactPerson = htmlspecialchars($_POST["organizationContact"]);
    $email = filter_var($_POST["organizationEmail"], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST["organizationPhone"]);
    $services = htmlspecialchars($_POST["organizationServices"]);
    
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
    
    // Create a SQL query to insert the organization
    $sql = "INSERT INTO users (name, user_type, email) VALUES (?, 'organization', ?)";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo displayError("Error preparing statement: " . $conn->error);
        exit();
    }
    
    // Bind parameters and execute
    $stmt->bind_param("ss", $orgName, $email);
    
    if ($stmt->execute()) {
        // Get the last inserted ID (organization's user_id)
        $user_id = $conn->insert_id;
        
        // Additional metadata could be stored in an organizations table
        $sql2 = "INSERT INTO organization_details (user_id, contact_person, phone, services_offered) VALUES (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        
        if ($stmt2) {
            $stmt2->bind_param("isss", $user_id, $contactPerson, $phone, $services);
            $stmt2->execute();
            $stmt2->close();
        }
        
        // Display success message
        echo displaySuccess("Organization registration successful!", $orgName, $contactPerson, $email, $phone, $services);
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
 * @param string $orgName Organization name
 * @param string $contactPerson Contact person
 * @param string $email Organization email
 * @param string $phone Organization phone
 * @param string $services Services offered
 * @return string HTML formatted success message with organization details
 */
function displaySuccess($message, $orgName, $contactPerson, $email, $phone, $services) {
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
                        <th>Organization Name:</th>
                        <td>' . $orgName . '</td>
                    </tr>
                    <tr>
                        <th>Contact Person:</th>
                        <td>' . $contactPerson . '</td>
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
                        <th>Services Offered:</th>
                        <td>' . nl2br($services) . '</td>
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