<?php
header('Content-Type:text/csv');
header('Content-Disposition:attachment;filename=gpa_results.csv');

$conn = new mysqli("localhost","root","","gpa_db");
$res = $conn->query("SELECT * FROM results");

echo "Name,Semester,GPA\n";

while($row=$res->fetch_assoc()){
echo "{$row['student_name']},{$row['semester']},{$row['gpa']}\n";
}
?>
