<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";
$serviceData = null;

// Fetch all services for dropdown
$servicesQuery = "SELECT service_id, title, category FROM services ORDER BY title, category";
$servicesResult = $conn->query($servicesQuery);

// Fetch all providers (organizations and volunteers) for dropdown
$providersQuery = "SELECT user_id, name, user_type FROM users WHERE user_type IN ('organization', 'volunteer') ORDER BY name";
$providersResult = $conn->query($providersQuery);

// Get service title options
$titleQuery = "SELECT DISTINCT title FROM services";
$titleResult = $conn->query($titleQuery);
$titles = [];
if ($titleResult && $titleResult->num_rows > 0) {
    while($row = $titleResult->fetch_assoc()) {
        $titles[] = $row['title'];
    }
}

// Get service category options
$categoryQuery = "SELECT DISTINCT category FROM services";
$categoryResult = $conn->query($categoryQuery);
$categories = [];
if ($categoryResult && $categoryResult->num_rows > 0) {
    while($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Process service selection
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['service_id'])) {
    $serviceId = $_GET['service_id'];
    
    $fetchQuery = "SELECT * FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($fetchQuery);
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $serviceData = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>Service not found.</div>";
    }
    
    $stmt->close();
}

// Process update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $serviceId = $_POST['service_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $providerId = $_POST['provider_id'];
    
    $updateQuery = "UPDATE services SET title = ?, category = ?, provider_id = ? WHERE service_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssii", $title, $category, $providerId, $serviceId);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Service updated successfully!</div>";
        
        // Refresh service data
        $fetchQuery = "SELECT * FROM services WHERE service_id = ?";
        $fetchStmt = $conn->prepare($fetchQuery);
        $fetchStmt->bind_param("i", $serviceId);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $serviceData = $result->fetch_assoc();
        }
        
        $fetchStmt->close();
    } else {
        $message = "<div class='alert alert-danger'>Error updating service: " . $conn->error . "</div>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Service - Senior Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-bg {
            background-image: url('background.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }
        .content-wrapper {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 30px;
            margin-top: 20px;
            margin-bottom: 50px;
        }
        /* Styles for the moving text banner */
        .text-banner {
            width: 100%;
            background-color: #0d6efd;
            color: white;
            padding: 10px 0;
            overflow: hidden;
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }
        .moving-text {
            white-space: nowrap;
            animation: moveText 20s linear infinite;
        }
        @keyframes moveText {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .footer-padding {
            padding-bottom: 50px;
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

    <main class="container mb-5">
        <div class="content-wrapper">
            <h2 class="text-center mb-4">Update Service</h2>
            
            <?php echo $message; ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Select Service to Update</h4>
                        </div>
                        <div class="card-body">
                            <form method="get" class="mb-3">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Select Service:</label>
                                    <select name="service_id" id="service_id" class="form-select" required>
                                        <option value="">-- Select a Service --</option>
                                        <?php 
                                        if ($servicesResult && $servicesResult->num_rows > 0) {
                                            while($row = $servicesResult->fetch_assoc()) {
                                                echo "<option value='" . $row['service_id'] . "'>" . 
                                                      $row['title'] . " - " . $row['category'] . " (ID: " . $row['service_id'] . ")" .
                                                     "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Select</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <?php if ($serviceData): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Update Service Details</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="service_id" value="<?php echo $serviceData['service_id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <select name="title" id="title" class="form-select" required>
                                        <?php foreach ($titles as $title): ?>
                                            <option value="<?php echo $title; ?>" <?php echo ($serviceData['title'] == $title) ? 'selected' : ''; ?>>
                                                <?php echo $title; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category:</label>
                                    <select name="category" id="category" class="form-select" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category; ?>" <?php echo ($serviceData['category'] == $category) ? 'selected' : ''; ?>>
                                                <?php echo $category; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="provider_id" class="form-label">Provider:</label>
                                    <select name="provider_id" id="provider_id" class="form-select" required>
                                        <?php 
                                        if ($providersResult && $providersResult->num_rows > 0) {
                                            $providersResult->data_seek(0); // Reset the pointer
                                            while($row = $providersResult->fetch_assoc()) {
                                                echo "<option value='" . $row['user_id'] . "' " . 
                                                     ($serviceData['provider_id'] == $row['user_id'] ? 'selected' : '') . ">" . 
                                                     $row['name'] . " (" . ucfirst($row['user_type']) . ")" .
                                                     "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" name="update_service" class="btn btn-success">Update Service</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <p class="lead mb-0">Please select a service to update from the list on the left</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.html" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 footer-padding">
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

    <!-- Moving Text Banner -->
    <div class="text-banner">
        <div class="moving-text">
            Welcome to the Senior Care website! Today is <span id="current-date"></span>, and the time is <span id="current-time"></span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to update date and time
        function updateDateTime() {
            const now = new Date();
            
            // Format date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);
            
            // Format time
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US');
        }
        
        // Update time initially and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>