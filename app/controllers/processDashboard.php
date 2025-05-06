<?php
// session_start();

// require_once 'db_connection.php';

// if (!isset($_SESSION['userId'])) {
//     header('Content-Type: application/json');
//     http_response_code(401); // Unauthorized
//     echo json_encode(['success' => false, 'message' => 'User not logged in.']);
//     exit;
// }
// $creatorUserId = $_SESSION['userId'];

// $title = $_POST['sessionTitle'] ?? null;
// $subjectName = $_POST['sessionSubject'] ?? null;
// $date = $_POST['sessionDate'] ?? null;
// $startTime = $_POST['sessionStartTime'] ?? null;
// $endTime = $_POST['sessionEndTime'] ?? null;
// $location = $_POST['sessionLocation'] ?? null;

// if (empty($title) || empty($subjectName) || empty($date) || empty($startTime) || empty($endTime) || empty($location)) {
//     header('Content-Type: application/json');
//     http_response_code(400); // Bad Request
//     echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
//     exit;
// }

// $response = ['success' => false, 'message' => 'An error occurred.']; // Default response

// try {
//     // --- 4a. Find the subjectID based on subjectName ---
//     // Reminder: A dropdown sending subjectID directly is usually better.
//     // We use a named placeholder ':subjectName' here.
//     $sql_subject = "SELECT subjectId FROM Subjects WHERE subjectName = :subjectName LIMIT 1";

//     $stmt_subject = $pdo->prepare($sql_subject);

//     $stmt_subject->execute(['subjectName' => $subjectName]);

//     $subjectID = $stmt_subject->fetchColumn(); // Gets the subjectID directly, or false if not found

//     if ($subjectID === false) {
//         // Subject not found. We throw an exception to be caught below.
//         throw new Exception("Subject not found: " . htmlspecialchars($subjectName));
//     }

//     // SQL query with question mark placeholders (?)
//     $sql_insert = "INSERT INTO ReviewSession
//                       (creatorUserId, subjectId, reviewTitle, reviewDate, reviewStartTime, reviewEndTime, reviewLocation, reviewStatus)
//                    VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled')"; // Default status 'scheduled'

//     $stmt_insert = $pdo->prepare($sql_insert);

//     $success = $stmt_insert->execute([
//         $creatorUserID,
//         $subjectID,
//         $title,
//         $date,
//         $startTime,
//         $endTime,
//         $location
//     ]);

//     // Check if the execution was successful
//     if ($success) {
//         // Get the ID of the inserted row
//         $newSessionId = $pdo->lastInsertId();

//         $response = [
//             'success' => true,
//             'message' => 'Session created successfully!',
//             'new_session_id' => $newSessionId // Optionally return the new ID
//         ];
//     } else {
//         // While execute() returns true/false, prepare() or execute() failures usually
//         // throw exceptions with ERRMODE_EXCEPTION set, so this 'else' might not be reached often.
//         // It's here as a fallback. More specific errors are caught below.
//          throw new Exception("Failed to execute the insert statement.");
//     }

// } catch (PDOException $e) {
//     // Catch errors specific to PDO operations (connection, query errors)
//     // Log the detailed error for developers
//     error_log("PDO Error in add_session_pdo.php: " . $e->getMessage());
//     // Provide a generic error message to the user
//     $response['message'] = "A database error occurred while adding the session.";
//     http_response_code(500); // Internal Server Error
// } catch (Exception $e) {
//     // Catch other general errors (like our custom "Subject not found" exception)
//     error_log("General Error in add_session_pdo.php: " . $e->getMessage());
//     $response['message'] = $e->getMessage(); // Use the specific error message here
//     // Decide on appropriate HTTP status code (e.g., 400 Bad Request if input related)
//     if (str_starts_with($e->getMessage(), "Subject not found:")) {
//          http_response_code(400);
//     } else {
//          http_response_code(500);
//     }
// }

// // 5. --- Send Response ---
// // This part remains the same - send JSON back to the JavaScript.
// header('Content-Type: application/json');
// echo json_encode($response);

// // No explicit close needed for $pdo usually. PHP handles it.
// // You can set $pdo = null; if you want to explicitly close earlier.
?>