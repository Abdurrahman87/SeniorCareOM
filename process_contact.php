<?php
/**
 * Process Contact Form Submission
 * This script receives data from the contact form, validates it, 
 * and displays a confirmation message along with the submitted information.
 */

// Initialize variables to store form data
$name = $email = $subject = $message = '';
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

    // Validate and sanitize subject
    if (empty($_POST['subject'])) {
        $errors[] = 'Subject is required';
    } else {
        $subject = htmlspecialchars(trim($_POST['subject']));
    }

    // Validate and sanitize message
    if (empty($_POST['message'])) {
        $errors[] = 'Message is required';
    } else {
        $message = htmlspecialchars(trim($_POST['message']));
    }

    // If no validation errors, proceed with processing
    if (empty($errors)) {
        // Create a class to represent a contact submission
        class ContactSubmission {
            public $name;
            public $email;
            public $subject;
            public $message;
            public $submissionDate;

            public function __construct($name, $email, $subject, $message) {
                $this->name = $name;
                $this->email = $email;
                $this->subject = $subject;
                $this->message = $message;
                $this->submissionDate = date('Y-m-d H:i:s');
            }
        }

        // Create a new contact submission object
        $contactSubmission = new ContactSubmission($name, $email, $subject, $message);

    }
}

// Function to display contact information in a table
function displayContactTable($submission) {
    echo '<table class="table table-striped">';
    echo '<tr><th>Field</th><th>Value</th></tr>';
    echo '<tr><td>Name</td><td>' . $submission->name . '</td></tr>';
    echo '<tr><td>Email</td><td>' . $submission->email . '</td></tr>';
    echo '<tr><td>Subject</td><td>' . $submission->subject . '</td></tr>';
    echo '<tr><td>Message</td><td>' . nl2br($submission->message) . '</td></tr>';
    echo '<tr><td>Submission Date</td><td>' . $submission->submissionDate . '</td></tr>';
    echo '</table>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submission - Senior Care</title>
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
                        <a href="contact us.html" class="btn btn-primary mt-3">Go Back</a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0">Thank You for Contacting Us!</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Your message has been received</h4>
                            <p class="card-text">We will review your inquiry and get back to you as soon as possible. Here's a summary of the information you provided:</p>
                            
                            <?php displayContactTable($contactSubmission); ?>
                            
                            <div class="text-center mt-4">
                                <a href="index.html" class="btn btn-primary me-2">Return to Home</a>
                                <a href="contact us.html" class="btn btn-outline-primary">Send Another Message</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h3>Form Not Submitted</h3>
                    <p>Please use the contact form to submit your message.</p>
                    <a href="contact us.html" class="btn btn-primary">Go to Contact Form</a>
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