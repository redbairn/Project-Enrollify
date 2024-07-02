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

$course_endpoint = new Courses($api_key, $domain);
$courses_response = $course_endpoint->get_courses();
$courses = $courses_response->courses;

// Calculate status counts
$status_counts = [
    'all' => count($enrollments),
    'failed' => 0,
    'passed' => 0,
    'completed' => 0,
    'not_started' => 0,
    'in_progress' => 0,
];

foreach ($enrollments as $enrollment) {
    if (isset($status_counts[$enrollment->status])) {
        $status_counts[$enrollment->status]++;
    }
}
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
    </div><!-- End header row -->

    <!-- Filter Form -->
    <div class="row filter-row mb-4">
        <div class="col">
            <label for="status-filter">Filter by Status:</label>
            <select id="status-filter" class="form-control">
                <option value="all">All (<?php echo $status_counts['all']; ?>)</option>
                <option value="failed">Failed (<?php echo $status_counts['failed']; ?>)</option>
                <option value="passed">Passed (<?php echo $status_counts['passed']; ?>)</option>
                <option value="completed">Completed (<?php echo $status_counts['completed']; ?>)</option>
                <option value="not_started">Not Started (<?php echo $status_counts['not_started']; ?>)</option>
                <option value="in_progress">In Progress (<?php echo $status_counts['in_progress']; ?>)</option>
            </select>
        </div>
    </div>

    <div id="courses-container">
        <?php 
        // Split enrollments into chunks of 5 for each row
        $chunks = array_chunk($enrollments, 5);
        foreach ($chunks as $chunk):
        ?>
        <div class="row course-row">
            <?php foreach ($chunk as $enrollment): ?>
                <?php
                // Find the corresponding course for this enrollment
                $course = null;
                foreach ($courses as $c) {
                    if ($c->id == $enrollment->course_id) {
                        $course = $c;
                        break;
                    }
                }
                if (!$course) continue; // Skip if course not found

                // Display course box with enrollment and course details
                ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4 course-box-container" data-status="<?php echo $enrollment->status; ?>">
                <div class="course-box">
                    <div class="course-header">
                        <p><?php echo $enrollment->course_name; ?></p>
                    </div>
                    <div class="thumbnail-placeholder">
                        <?php if (filter_var($course->thumbnail_image_url, FILTER_VALIDATE_URL)): ?>
                            <img src="<?php echo $course->thumbnail_image_url; ?>" alt="<?php echo $enrollment->course_name; ?>" class="course-thumbnail">
                        <?php else: ?>
                            <!-- Placeholder for the course thumbnail -->
                            <p>No Thumbnail Available</p>
                        <?php endif; ?>
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
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusCounts = <?php echo json_encode($status_counts); ?>;
    const statusFilter = document.getElementById("status-filter");
    const courseBoxes = Array.from(document.querySelectorAll(".course-box-container"));
    const coursesContainer = document.getElementById("courses-container");

    statusFilter.addEventListener("change", function() {
        const selectedStatus = statusFilter.value;

        // Clear the container
        coursesContainer.innerHTML = "";

        // Filter course boxes based on selected status
        const filteredBoxes = courseBoxes.filter(box => selectedStatus === "all" || box.getAttribute("data-status") === selectedStatus);

        // Split filtered boxes into chunks of 5
        const chunks = [];
        for (let i = 0; i < filteredBoxes.length; i += 5) {
            chunks.push(filteredBoxes.slice(i, i + 5));
        }

        // Append filtered and chunked boxes to the container
        chunks.forEach(chunk => {
            const row = document.createElement("div");
            row.className = "row course-row";
            chunk.forEach(box => {
                const clonedBox = box.cloneNode(true); // Clone the box to keep it for future filters
                row.appendChild(clonedBox);
            });
            coursesContainer.appendChild(row);
        });

        // Update the counts in the filter options
        updateFilterCounts(statusCounts);
    });

    // Function to update filter counts
    function updateFilterCounts(statusCounts) {
        document.querySelector('option[value="all"]').textContent = `All (${statusCounts['all']})`;
        document.querySelector('option[value="failed"]').textContent = `Failed (${statusCounts['failed']})`;
        document.querySelector('option[value="passed"]').textContent = `Passed (${statusCounts['passed']})`;
        document.querySelector('option[value="completed"]').textContent = `Completed (${statusCounts['completed']})`;
        document.querySelector('option[value="not_started"]').textContent = `Not Started (${statusCounts['not_started']})`;
        document.querySelector('option[value="in_progress"]').textContent = `In Progress (${statusCounts['in_progress']})`;
    }
});
</script>

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
