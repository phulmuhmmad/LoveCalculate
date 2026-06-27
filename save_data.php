<?php
// Get the data from the AJAX request
$yourName = isset($_POST['yourName']) ? trim($_POST['yourName']) : '';
$partnerName = isset($_POST['partnerName']) ? trim($_POST['partnerName']) : '';
$loveScore = isset($_POST['loveScore']) ? intval($_POST['loveScore']) : 0;

// Validate input
if (empty($yourName) || empty($partnerName)) {
    echo 'Error: Names cannot be empty';
    exit;
}

// CSV file path
$csvFile = 'love_data.csv';

// Prepare the data
$timestamp = date('Y-m-d H:i:s');
$data = array($yourName, $partnerName, $loveScore, $timestamp);

// Check if file exists, if not create with headers
if (!file_exists($csvFile)) {
    $file = fopen($csvFile, 'w');
    fputcsv($file, array('Your Name', 'Partner Name', 'Love Score', 'Timestamp'));
} else {
    $file = fopen($csvFile, 'a');
}

// Write the data to CSV
if ($file) {
    fputcsv($file, $data);
    fclose($file);
    echo 'Success: Data saved to CSV';
} else {
    echo 'Error: Could not open CSV file';
}
?>
