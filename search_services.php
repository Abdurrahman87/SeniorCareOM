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
$searchTerm = '';
$searchResults = [];
$searchPerformed = false;

// Process search form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $searchPerformed = true;
    
    // Prepare the search query
    $sql = "SELECT s.service_id, s.title, s.category, u.name as provider_name 
            FROM services s
            JOIN users u ON s.provider_id = u.user_id
            WHERE s.title LIKE ? 
            OR s.category LIKE ?
            OR u.name LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch all results into an array
        while($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Services - Senior Care</title>
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
                            <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="contact us.html">Contact Us</a></li>
                            <li class="nav-item"><a class="nav-link" href="about us.html">About Us</a></li>
                            <li class="nav-item"><a class="nav-link" href="services directory.html">Services Directory</a></li>
                            <li class="nav-item"><a class="nav-link active" href="search_services.php">Search Services</a></li>
                            <li class="nav-item"><a class="nav-link" href="forum page.html">Community Forum</a></li>
                            <li class="nav-item"><a class="nav-link" href="resource_page.html">Health and Wellness Resource Center</a></li>
                            <li class="nav-item"><a class="nav-link" href="volunteers and organizations.html">Volunteers and Organizations</a></li>
                            <li class="nav-item"><a class="nav-link" href="events.html">Events</a></li>
                            <li class="nav-item"><a class="nav-link" href="add_service.php">Add Service</a></li>
                            <li class="nav-item"><a class="nav-link" href="update_service.php">Update Service</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Search Services</h2>
                
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Search Database</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="input-group mb-3">
                                <input type="text" name="searchTerm" class="form-control" 
                                       placeholder="Search by service title, category, or provider" 
                                       value="<?php echo htmlspecialchars($searchTerm); ?>" required>
                                <button class="btn btn-primary" type="submit" name="search">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Search Results -->
                <?php if ($searchPerformed): ?>
                    <div class="card mb-5">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0">Search Results</h3>
                        </div>
                        <div class="card-body">
                            <?php if (count($searchResults) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Service ID</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Provider</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($searchResults as $service): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($service['service_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['category']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['provider_name']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No services found matching your search criteria.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
