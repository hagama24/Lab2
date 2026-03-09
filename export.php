<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=gpa_results.csv');

$conn = new mysqli("localhost", "root", "", "gpa_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT student_name, semester, gpa FROM results");

$output = fopen('php://output', 'w');

fputcsv($output, ['Name', 'Semester', 'GPA']);

while ($row = $res->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conn->close();
?>
