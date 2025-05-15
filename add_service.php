<?php
// Database connection
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
$success = false;

// Get provider list
$providers = [];
$providerQuery = "SELECT user_id, name FROM users WHERE user_type = 'organization' OR user_type = 'volunteer'";
$providerResult = $conn->query($providerQuery);
if ($providerResult->num_rows > 0) {
    while($row = $providerResult->fetch_assoc()) {
        $providers[] = $row;
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addService'])) {
    // Get form data
    $title = $_POST['title'];
    $category = $_POST['category'];
    $provider_id = $_POST['provider_id'];
    
    // Validate form data
    if (empty($title) || empty($category) || empty($provider_id)) {
        $message = "All fields are required!";
    } else {
        // Prepare SQL statement to prevent SQL injections
        $sql = "INSERT INTO services (title, category, provider_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $category, $provider_id);
        
        // Execute statement
        if ($stmt->execute()) {
            $message = "New service added successfully!";
            $success = true;
        } else {
            $message = "Error: " . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - Senior Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-bg {
            background-image: url('background.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
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

    <main class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Add New Service</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> mb-4">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-5">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Service Information</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-3">
                                <label for="title" class="form-label">Service Title</label>
                                <select id="title" name="title" class="form-select" required>
                                    <option value="">Select a service title</option>
                                    <option value="Health Services">Health Services</option>
                                    <option value="Daily Living Support">Daily Living Support</option>
                                    <option value="Social Activities">Social Activities</option>
                                    <option value="Professional Services">Professional Services</option>
                                    <option value="Transportation Services">Transportation Services</option>
                                    <option value="Technology Assistance">Technology Assistance</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Service Category</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="">Select a category</option>
                                    <option value="Health Consultation">Health Consultation</option>
                                    <option value="Mental Health Support">Mental Health Support</option>
                                    <option value="Caregiver Assistance">Caregiver Assistance</option>
                                    <option value="Home Repair Services">Home Repair Services</option>
                                    <option value="Grocery Delivery">Grocery Delivery</option>
                                    <option value="Social Events">Social Events</option>
                                    <option value="Legal Aid">Legal Aid</option>
                                    <option value="Financial Planning">Financial Planning</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Technology Support">Technology Support</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="provider_id" class="form-label">Service Provider</label>
                                <select id="provider_id" name="provider_id" class="form-select" required>
                                    <option value="">Select a provider</option>
                                    <?php foreach ($providers as $provider): ?>
                                        <option value="<?php echo $provider['user_id']; ?>">
                                            <?php echo htmlspecialchars($provider['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="addService" class="btn btn-primary">Add Service</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4">
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

<?php
// Close the database connection
$conn->close();
?>
