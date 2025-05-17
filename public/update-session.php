<?php
// Ensure errors are logged but not displayed, to keep JSON output clean
ini_set('display_errors', 0); // Turn off displaying errors to the browser
ini_set('log_errors', 1); // Enable error logging
// error_log("update-session.php: Script started, display_errors is OFF."); // Optional: for debugging logs

error_reporting(E_ALL); // Report all errors for logging

require_once __DIR__ . '/../app/config/init.php';
use App\Models\StudySession; // Import the StudySession model

// Ensure we always output JSON
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

try {
    // 1. Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method.';
        echo json_encode($response);
        exit;
    }

    // 2. CSRF Token Validation
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $response['message'] = 'Invalid or missing CSRF token.';
        // For debugging CSRF:
        // $response['session_csrf'] = $_SESSION['csrf_token'] ?? 'not set';
        // $response['post_csrf'] = $_POST['csrf_token'] ?? 'not set';
        echo json_encode($response);
        exit;
    }

    // 3. Get and Validate Input Data
    $requiredFields = [
        'reviewSessionID', 'reviewTitle', 'subjectID', 'reviewTopic',
        'reviewDate', 'reviewStartTime', 'reviewEndTime', 'reviewLocation'
    ];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        // Allow reviewDescription to be empty, but other fields are required.
        // reviewTopic is also in requiredFields, so it must not be empty.
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        $response['message'] = 'Missing required fields: ' . implode(', ', $missingFields);
        echo json_encode($response);
        exit;
    }

    $sessionId = trim($_POST['reviewSessionID']);

    // Prepare data for the model's updateSession method
    $dataToUpdate = [
        'subjectID'         => trim($_POST['subjectID']),
        'reviewTitle'       => trim($_POST['reviewTitle']),
        'reviewDate'        => trim($_POST['reviewDate']),
        'reviewStartTime'   => trim($_POST['reviewStartTime']),
        'reviewEndTime'     => trim($_POST['reviewEndTime']),
        'reviewLocation'    => trim($_POST['reviewLocation']),
        'reviewDescription' => isset($_POST['reviewDescription']) ? trim($_POST['reviewDescription']) : '', // Optional
        'reviewTopic'       => trim($_POST['reviewTopic']), // Required as per $requiredFields
        // 'reviewStatus' can be added here if it's part of the form and updatable.
        // e.g., 'reviewStatus' => isset($_POST['reviewStatus']) ? trim($_POST['reviewStatus']) : 'scheduled',
        // If not provided, the model's updateSession method defaults it.
    ];

    // Basic validation for session ID format
    if (!ctype_digit($sessionId) || (int)$sessionId <= 0) {
        $response['message'] = 'Invalid session ID format.';
        echo json_encode($response);
        exit;
    }
    // Further validation (e.g., date/time formats, subjectID existence) is handled by the model's validateSessionData.

    // 4. Database Connection (ensure $pdo is available)
    $pdo = require __DIR__ . '/../app/config/db_connection.php';
    if (!$pdo) {
        $response['message'] = 'Database connection failed.';
        error_log('update-session.php: Database connection failed.');
        echo json_encode($response);
        exit;
    }

    // 5. Instantiate the Model
    $studySessionModel = new StudySession($pdo);

    // 6. Call the updateSession method from the model
    $updateResult = $studySessionModel->updateSession($sessionId, $dataToUpdate);

    // 7. Process the result from the model
    if ($updateResult['success']) {
        $response['success'] = true;
        $response['message'] = $updateResult['message']; // e.g., "Study session updated successfully." or "No changes detected."
        // Optionally, you can add the updated data to the response if the client needs it
        // $response['updated_data'] = $dataToUpdate;
        // $response['updated_data']['reviewSessionID'] = $sessionId;
    } else {
        $response['message'] = $updateResult['message'] ?? 'Failed to update study session.';
        if (isset($updateResult['errors'])) {
            $response['errors'] = $updateResult['errors']; // Validation errors from model
        }
        if (isset($updateResult['error_detail'])) { // Database execution errors
             $response['error_detail'] = $updateResult['error_detail'];
        }
        error_log('Update session failed for ID ' . $sessionId . '. Message: ' . $response['message'] . (isset($response['errors']) ? ' Errors: ' . print_r($response['errors'], true) : '') . (isset($response['error_detail']) ? ' Detail: ' . $response['error_detail'] : ''));
    }

} catch (PDOException $e) {
    error_log("PDOException in update-session.php: " . $e->getMessage());
    $response['success'] = false; // Ensure success is explicitly false
    $response['message'] = 'Database error during update.'; 
    $response['error_detail'] = $e->getMessage(); 
    echo json_encode($response); // Immediately send JSON response
    exit; // And exit
} catch (Exception $e) {
    error_log("Exception in update-session.php: " . $e->getMessage());
    $response['success'] = false; // Ensure success is explicitly false
    $response['message'] = 'An unexpected error occurred.'; 
    $response['error_detail'] = $e->getMessage(); 
    echo json_encode($response); // Immediately send JSON response
    exit; // And exit
}

// 8. Send JSON response (this will only be reached if no exceptions were caught and handled above)
echo json_encode($response);
exit;
?>