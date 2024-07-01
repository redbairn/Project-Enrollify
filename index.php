<?php 
include 'partials/header.php';
include 'partials/api_key.php'; 
include 'classes/Users.php';
include 'classes/Courses.php';
include 'classes/Enrollments.php';

// Initialize variables
$error_message = null;
$success_message = null;

try {
    $user_endpoint = new Users($api_key, $domain);
    $course_endpoint = new Courses($api_key, $domain);
    $enrollment_endpoint = new Enrollments($api_key, $domain);

    // Fetch users and courses data
    $users_response = $user_endpoint->get_users();
    if (!isset($users_response->user)) {
        throw new Exception("Users data not found in response");
    }
    $users = $users_response->user;

    $courses_response = $course_endpoint->get_courses();
    if (!isset($courses_response->courses)) {
        throw new Exception("Courses data not found in response");
    }
    $courses = $courses_response->courses;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])) {
            $email = $_POST['user_email'];
            $course_id = $_POST['select_course'];
            $user_created = false; // Flag to indicate if a new user was created

            try {
                // Check if the user exists
                $user_info_response = $user_endpoint->get_user_by_email($email);
                $user_exists = !empty($user_info_response) && !empty($user_info_response->user);

                if (!$user_exists) {
                    // User does not exist, create the user
                    $user_info = $user_endpoint->create_user($email);
                    $user_created = true; // Set the flag to true
                } else {
                    // User already exists, retrieve the user info
                    $user_info = $user_info_response->user[0];
                }

                // Get the user ID
                if (isset($user_info->id)) {
                    $user_id = $user_info->id;

                    // Enroll the user in the selected course
                    $enrollment_result = $enrollment_endpoint->create_enrollment($email, $course_id);

                    // Check if enrollment was successful
                    if (!empty($enrollment_result->id) && !empty($enrollment_result->created_at)) {
                        // Build success message
                        $success_message = "<p>User enrolled successfully!</p>";
                        if ($user_created) {
                            $success_message .= "User created successfully!<br>";
                        }
                        $success_message .= "<strong>Enrollment ID:</strong> {$enrollment_result->id}<br>";
                        $success_message .= "<strong>Creation Date:</strong> {$enrollment_result->created_at}<br>";
                        $success_message .= "<strong>User Email:</strong> {$email}<br>";
                    } else {
                        $error_message = "Error: Enrollment details not found in response.";
                    }
                } else {
                    // Handle the case where the user ID is not found
                    $error_message = "Error: User ID not found.";
                }
            } catch (Exception $e) {
                // Check if the exception is a 404 (user not found) to proceed with user creation
                if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                    $response = $e->getResponse();
                    $responseBody = json_decode($response->getBody()->getContents());
                    if ($responseBody->response_code == 404) {
                        // User not found, proceed to create the user
                        try {
                            $user_info = $user_endpoint->create_user($email);
                            $user_created = true; // Set the flag to true
                            // Get the user ID from the newly created user
                            if (isset($user_info->id)) {
                                $user_id = $user_info->id;
                                // Enroll the user in the selected course
                                $enrollment_result = $enrollment_endpoint->create_enrollment($email, $course_id);
                                // Check if enrollment was successful
                                if (!empty($enrollment_result->id) && !empty($enrollment_result->created_at)) {
                                    // Build success message
                                    if ($user_created) {
                                      $success_message = "<p>User created and enrolled successfully!</p>";
                                    }
                                    $success_message .= "<strong>Enrollment ID:</strong> {$enrollment_result->id}<br>";
                                    $success_message .= "<strong>Creation Date:</strong> {$enrollment_result->created_at}<br>";
                                    $success_message .= "<strong>User Email:</strong> {$email}<br>";
                                } else {
                                    $error_message = "Error: Enrollment details not found in response.";
                                }
                            } else {
                                $error_message = "Error: User ID not found after creation.";
                            }
                        } catch (Exception $e) {
                            $error_message = ucfirst($e->getMessage());
                        }
                    } else {
                        $error_message = ucfirst("{$responseBody->message} [Error Code: {$responseBody->response_code}]<br>");
                    }
                } else {
                    $error_message = ucfirst($e->getMessage());
                }
            }
        }
    }
} catch (Exception $e) {
    $error_message = ucfirst($e->getMessage());
    $users = [];
    $courses = [];
}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col col-left">
            <!-- Column 1 -->
        </div>
        <div class="col col-middle">
            <h1>Manager Dashboard</h1>

            <?php if ($error_message && isset($_POST['submit'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message && isset($_POST['submit'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <h2>Create Enrollment</h2>
            <form action="index.php" method="POST" id="enrollment_form">
                <ul>
                    <li>
                        <label for="mail">Email:</label>
                        <input type="email" id="mail" name="user_email" required />
                    </li>
                    <li>
                        <label for="courses">Course:</label>
                        <select id="courses" name="select_course">
                            <option value="" disabled selected>Choose Course</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course->id; ?>"><?php echo $course->name; ?></option>
                            <?php endforeach; ?> 
                        </select>
                    </li>
                </ul>
                <div class="button-container">
                    <button type="submit" name="submit">Enroll User</button>
                </div>
            </form>
        </div>
        <div class="col col-right">
            <!-- Column 3 -->
        </div>
    </div>
    <div class="row second-row">
        <div class="col col-left">
            <!-- Column 1 -->
        </div>
        <div class="col col-middle nav-column">
            <!-- Nav Form -->
            <h2>Navigation</h2>
            <form action="learner_dashboard.php" method="POST" id="nav_form">
                <ul>
                    <li>
                        <label for="users">Go to User Dashboard:</label>
                        <select id="users" name="select_user">
                            <option value="" disabled selected>Choose User</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?php echo $user->email; ?>" data-name="<?php echo $user->first_name; ?>"><?php echo $user->email; ?></option>
                            <?php endforeach; ?> 
                        </select>
                    </li>
                    <input type="hidden" id="user_fname" name="user_fname" value="">
                    <input type="hidden" id="user_email" name="user_email" value="">
                </ul>
                <div class="button-container">
                    <button type="submit" name="go">Go!</button>
                </div>
            </form>
        </div>
        <div class="col col-right">
            <!-- Column 3 -->
        </div>
    </div>
</div><!--End of Container Fluid-->
<script>
    // JavaScript to set the user_fname and user_email hidden fields based on selected user
    document.getElementById('users').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var userFname = selectedOption.getAttribute('data-name');
        var userEmail = selectedOption.value;
        document.getElementById('user_fname').value = userFname;
        document.getElementById('user_email').value = userEmail;
    });
</script>
<?php include 'partials/footer.php'; ?>


