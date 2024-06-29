<?php 
include 'partials/header.php';
include 'partials/api_key.php'; 
include 'classes/Users.php';
include 'classes/Courses.php';
include 'classes/Enrollments.php';

$lu_api = new Users($api_key, $domain);
$lu_api2 = new Courses($api_key, $domain);
$enroll_api = new Enrollments($api_key, $domain);

$courses_response = $lu_api2->get_courses();
$courses = $courses_response->courses; // Access the 'courses' array from the response object

$user_info = null;
$enrollment_result = null;
$error_message = null;
$success_message = null;

// Handle form submission
if (isset($_POST['submit'])) {
    $email = $_POST['user_email'];
    $course_id = $_POST['select_course'];
    $user_created = false; // Flag to indicate if a new user was created

    try {
        // Check if the user exists
        $user_info_response = $lu_api->get_user_by_email($email);

        $user_exists = !empty($user_info_response) && !empty($user_info_response->user);

        if (!$user_exists) {
            // User does not exist, create the user
            $user_info = $lu_api->create_user($email);
            $user_created = true; // Set the flag to true
        } else {
            // User already exists, retrieve the user info
            $user_info = $user_info_response->user[0];
        }

        // Get the user ID
        if (isset($user_info->id)) {
            $user_id = $user_info->id;

            // Enroll the user in the selected course
            $enrollment_result = $enroll_api->create_enrollment($email, $course_id);

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
                    $user_info = $lu_api->create_user($email);
                    $user_created = true; // Set the flag to true
                    // Get the user ID from the newly created user
                    if (isset($user_info->id)) {
                        $user_id = $user_info->id;
                        // Enroll the user in the selected course
                        $enrollment_result = $enroll_api->create_enrollment($email, $course_id);
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
?>


<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1>Manager Dashboard</h1>

            <?php if ($error_message) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>

            <?php if ($success_message) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php } ?>

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
                <?php 
                // Loop through the courses and create an option for each course
                foreach($courses as $course){ ?>
                <option value="<?php echo $course->id;?>"><?php echo $course->name;?>
                </option>
                <?php } ?> 
                </select>
              </li>
            </ul>
            <button type="submit" name="submit">Enroll User</button>
            </form>
        </div><!--End of col-->
    </div><!--End of row-->
    <!-- Start of second row-->
    <div class="row">
      <div class="col">
        <!--Column 1-->
      </div>
      <div class="col">
          <!--Nav Form-->
          <h2>Navigation</h2>
          </br>
          </br>
          <h3 style="text-align:center;">
          <small class="text-body-secondary">This will take you to the Learner's Dashboard.</small>
          </h3>
          <form action="user_dashboard.php" method="POST" id="nav_form">
          <ul>
          <li>
          <label for="users">Go to User Dashboard</label>
          <select id="users" name="select_user">
          <option value="" disabled selected>Choose User</option>

          <?php include 'partials/navigation.php'; ?>



          </select>
          </li>
          </ul>
          <button type="submit" name="go">Go!</button>
          </form> 




      </div> 
      <div class="col">
        <!--Column 3-->
      </div>
    </div><!-- Start of second row-->



  </div><!--End of Fluid Container-->

<?php 
  include 'partials/footer.php'; 
?>