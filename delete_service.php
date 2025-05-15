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
$deleteResult = false;

// Process delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_service'])) {
    $serviceId = $_POST['service_id'];
    
    // First check if there are any registrations for this service
    $checkQuery = "SELECT COUNT(*) as count FROM registrations WHERE service_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $serviceId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $row = $checkResult->fetch_assoc();
    
    if ($row['count'] > 0) {
        $message = "<div class='alert alert-danger'>Cannot delete service as it has active registrations. Please remove registrations first.</div>";
    } else {
        // Proceed with deletion
        $deleteQuery = "DELETE FROM services WHERE service_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $serviceId);
        
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Service deleted successfully!</div>";
            $deleteResult = true;
        } else {
            $message = "<div class='alert alert-danger'>Error deleting service: " . $conn->error . "</div>";
        }
        
        $stmt->close();
    }
}

// Fetch all services for display
$sql = "SELECT s.service_id, s.title, s.category, u.name as provider_name 
        FROM services s 
        LEFT JOIN users u ON s.provider_id = u.user_id 
        ORDER BY s.service_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service - Senior Care</title>
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
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .delete-btn:hover {
            background-color: #c82333;
            color: white;
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
            <h2 class="text-center mb-4">Delete Service</h2>
            
            <?php echo $message; ?>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Available Services</h4>
                </div>
                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Provider</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['service_id']; ?></td>
                                        <td><?php echo $row['title']; ?></td>
                                        <td><?php echo $row['category']; ?></td>
                                        <td><?php echo $row['provider_name']; ?></td>
                                        <td>
                                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                <input type="hidden" name="service_id" value="<?php echo $row['service_id']; ?>">
                                                <button type="submit" name="delete_service" class="btn btn-sm delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No services found in the database.</p>
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