<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;
$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

$valid_statuses = ['Scheduled', 'Ongoing', 'Completed', 'Cancelled'];

if ($match_id && in_array($status, $valid_statuses)) {
    $query = "UPDATE matches SET status = '$status' WHERE match_id = $match_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'status' => $status]);
    } else {
        echo json_encode(['error' => 'Failed to update match status']);
    }
} else {
    echo json_encode(['error' => 'Invalid data']);
}

mysqli_close($conn);
?>
