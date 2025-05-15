<?php
/**
 * Process Feedback Form Submission
 * This script receives data from the feedback questionnaire form, 
 * validates it, and displays the results in a formatted HTML table.
 */

// Initialize variables to store form data
$name = $email = $rating = $feedback = '';
$features = [];
$errors = [];

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize name
    if (empty($_POST['name'])) {
        $errors[] = 'Name is required';
    } else {
        $name = htmlspecialchars(trim($_POST['name']));
    }

    // Validate and sanitize email
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required';
    } else {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }
    }

    // Validate and sanitize rating
    if (empty($_POST['rating'])) {
        $errors[] = 'Please select a rating';
    } else {
        $rating = htmlspecialchars($_POST['rating']);
    }

    // Validate and sanitize features
    if (empty($_POST['features']) || !is_array($_POST['features'])) {
        $errors[] = 'Please select at least one feature';
    } else {
        foreach ($_POST['features'] as $feature) {
            $features[] = htmlspecialchars($feature);
        }
    }

    // Sanitize feedback (optional field)
    if (!empty($_POST['feedback'])) {
        $feedback = htmlspecialchars(trim($_POST['feedback']));
    }

    // If no validation errors, proceed with processing
    if (empty($errors)) {
        // Create a class to represent a feedback record
        class FeedbackRecord {
            public $name;
            public $email;
            public $rating;
            public $features;
            public $feedback;
            public $submissionDate;

            public function __construct($name, $email, $rating, $features, $feedback) {
                $this->name = $name;
                $this->email = $email;
                $this->rating = $rating;
                $this->features = $features;
                $this->feedback = $feedback;
                $this->submissionDate = date('Y-m-d H:i:s');
            }
        }

        // Create a new feedback record object
        $feedbackRecord = new FeedbackRecord($name, $email, $rating, $features, $feedback);


    }
}

// Function to iterate over an array and display the data in a table
function displayFeedbackTable($record) {
    echo '<table class="table table-striped">';
    echo '<tr><th>Field</th><th>Value</th></tr>';
    echo '<tr><td>Name</td><td>' . $record->name . '</td></tr>';
    echo '<tr><td>Email</td><td>' . $record->email . '</td></tr>';
    echo '<tr><td>Rating</td><td>' . $record->rating . '</td></tr>';
    echo '<tr><td>Features</td><td>' . implode(', ', $record->features) . '</td></tr>';
    echo '<tr><td>Feedback</td><td>' . ($record->feedback ? $record->feedback : 'No feedback provided') . '</td></tr>';
    echo '<tr><td>Submission Date</td><td>' . $record->submissionDate . '</td></tr>';
    echo '</table>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Submission - Senior Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-bg {
            background-image: url('background.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }
        .result-container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .footer-padding {
            padding-bottom: 20px;
        }
    </style>
</head>
<body class="custom-bg">
    <header class="bg-primary text-white p-3">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="mb-0">Senior Care</h1>
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link active" href="index.html">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="contact us.html">Contact Us</a></li>
                            <li class="nav-item"><a class="nav-link" href="about us.html">About Us</a></li>
                            <li class="nav-item"><a class="nav-link" href="services directory.html">Services Directory</a></li>
                            <li class="nav-item"><a class="nav-link" href="search_services.php">Search Services</a></li>
                            <li class="nav-item"><a class="nav-link" href="forum page.html">Community Forum</a></li>
                            <li class="nav-item"><a class="nav-link" href="resource_page.html">Health and Wellness Resource Center</a></li>
                            <li class="nav-item"><a class="nav-link" href="volunteers and organizations.html">Volunteers and Organizations</a></li>
                            <li class="nav-item"><a class="nav-link" href="events.html">Events</a></li>
                            <li class="nav-item"><a class="nav-link" href="questionnaire page.html">Feedback Questionnaire</a></li>
                            <li class="nav-item"><a class="nav-link" href="calculate page.html">Service Bill Calculator</a></li>
                            <li class="nav-item"><a class="nav-link" href="funpage.html">Fun Math Game</a></li>
                            <li class="nav-item"><a class="nav-link active" href="delete_service.php">Delete Service</a></li>
                            <li class="nav-item"><a class="nav-link" href="update_service.php">Update Service</a></li>
                            <li class="nav-item"><a class="nav-link active" href="add_service.php">Add Service</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <div class="result-container">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h3>Please correct the following errors:</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="questionnaire page.html" class="btn btn-primary mt-3">Go Back</a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0">Thank You for Your Feedback!</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Your submission has been received</h4>
                            <p class="card-text">Here's a summary of the information you provided:</p>
                            
                            <?php displayFeedbackTable($feedbackRecord); ?>
                            
                            <div class="text-center mt-4">
                                <a href="index.html" class="btn btn-primary me-2">Return to Home</a>
                                <a href="questionnaire page.html" class="btn btn-outline-primary">Submit Another Response</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h3>Form Not Submitted</h3>
                    <p>Please use the feedback form to submit your response.</p>
                    <a href="questionnaire page.html" class="btn btn-primary">Go to Feedback Form</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 footer-padding mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h3>Contact Info:</h3>
                    <p>Phone: +968 7845 2247</p>
                    <p>Email: SeniorCareOM@gmail.com</p>
                    <p>Copyright © 2025 SeniorCareOM</p>
                </div>
                <div class="col-md-4 text-end">
                    <img src="logo.jpg" class="img-fluid" style="width: 100px; height: 100px;" alt="logo">
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>