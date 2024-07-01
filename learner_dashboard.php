<?php 
include 'partials/header.php';
include 'partials/api_key.php'; 
include 'classes/Users.php';
include 'classes/Courses.php';
include 'classes/Enrollments.php';

// Fetch user name from POST
$user_fname = isset($_POST['user_fname']) ? $_POST['user_fname'] : '';
$email = isset($_POST['user_email']) ? $_POST['user_email'] : '';

$enrollment_endpoint = new Enrollments($api_key, $domain);
$enrollments_response = $enrollment_endpoint->get_enrollments_by_email($email);
$enrollments = $enrollments_response->enrollments;

?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Enrollify</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row header-row">
        <div class="col">
            <h1>Learner Dashboard</h1>
        </div>
        <div class="col-auto">
            <div class="welcome-message">
                <p>Welcome, <?php echo htmlspecialchars($user_fname); ?>!</p>
            </div>
        </div>
    </div><!--End header row-->

    <?php 
    // Split enrollments into chunks of 5 for each row
    $chunks = array_chunk($enrollments, 5);
    foreach ($chunks as $chunk):
    ?>
    <div class="row">
        <?php foreach ($chunk as $enrollment): ?>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
            <div class="course-box">
                <div class="course-header">
                    <p><?php echo $enrollment->course_name; ?></p>
                </div>
                <div class="thumbnail-placeholder">
                    <!-- Placeholder for the course thumbnail -->
                </div>
                <ul class="course-info">
                    <li><strong>Enrollment ID:</strong> <?php echo $enrollment->id; ?></li>
                    <li><strong>Course ID:</strong> <?php echo $enrollment->course_id; ?></li>
                    <li><strong>Date Enrolled:</strong> <?php echo format_date($enrollment->date_enrolled); ?></li>
                    <li><strong>Date Completed:</strong> <?php echo format_date($enrollment->date_completed); ?></li>
                    <?php if (!empty($enrollment->percentage)): ?>
                        <li><strong>Percentage:</strong> <?php echo $enrollment->percentage; ?></li>
                    <?php endif; ?>
                </ul>
                <div class="status-bar <?php echo format_status_class($enrollment->status); ?>">
                    <p><?php echo format_status($enrollment->status); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'partials/footer.php'; 

// Function to format the date
function format_date($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M, jS Y');
}

// Function to format status
function format_status($status) {
    // Replace underscores and convert to uppercase
    return ucwords(str_replace('_', ' ', $status));
}

// Function to determine status class
function format_status_class($status) {
    switch ($status) {
        case 'failed':
            return 'failed';
        case 'passed':
        case 'completed':
            return 'passed';
        case 'not_started':
            return 'not-started';
        case 'in_progress':
            return 'in-progress';
        default:
            return '';
    }
}
?>