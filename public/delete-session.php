<?php
require_once __DIR__ . '/../app/config/init.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.']; // Default response

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// CSRF Token Validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $response['message'] = 'Invalid CSRF token.';
    // For debugging:
    // $response['debug_session_csrf'] = $_SESSION['csrf_token'] ?? 'not set';
    // $response['debug_post_csrf'] = $_POST['csrf_token'] ?? 'not set';
    echo json_encode($response);
    exit;
}

$pdo = require __DIR__ . '/../app/config/db_connection.php';
if (!$pdo) {
    $response['message'] = 'Database connection failed.';
    error_log('delete-session.php: Database connection failed.');
    echo json_encode($response);
    exit;
}

$sessionId = $_POST['reviewSessionID'] ?? null;

if (empty($sessionId)) {
    $response['message'] = 'Session ID is required.';
    echo json_encode($response);
    exit;
}

// Optional: Check if the session belongs to the current user or if user has rights to delete
// This part is commented out in the original, but if re-enabled, ensure it sets $response and allows flow to json_encode/exit
// $studySessionModel = new \\App\\Models\\StudySession($pdo);
// $sessionDetails = $studySessionModel->getSessionById($sessionId);
// if (!$sessionDetails || $sessionDetails['creatorUserID'] != $_SESSION['userId']) {
// $response['message'] = 'You do not have permission to delete this session.';
// echo json_encode($response);
// exit;
// }

try {
    $stmt = $pdo->prepare("DELETE FROM reviewsession WHERE reviewSessionID = :reviewSessionID");
    
    $executionSuccess = $stmt->execute([':reviewSessionID' => $sessionId]);

    if ($executionSuccess) {
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Study session deleted successfully!'; // Consistent with screenshot
        } else {
            $response['success'] = false; // Explicitly set success to false if no rows affected
            $response['message'] = 'Session not found, already deleted, or you do not have permission to delete it. No rows affected.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to execute delete statement.';
        error_log('delete-session.php: SQL execution error: ' . implode(", ", $stmt->errorInfo() ?: ['No error info available'])); // Log actual error
    }

} catch (PDOException $e) {
    error_log("Delete session PDOException: " . $e->getMessage());
    $response['success'] = false; // Ensure success is false on exception
    $response['message'] = 'Database error during deletion. Please check logs.';
} catch (Exception $e) {
    error_log("Delete session Exception: " . $e->getMessage());
    $response['success'] = false; // Ensure success is false on exception
    $response['message'] = 'An unexpected error occurred during deletion.';
}

echo json_encode($response);
exit;
?>
