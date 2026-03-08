<?php
$conn = new mysqli("localhost","root","","gpa_db");
if($conn->connect_error){
die("Connection failed: " . $conn->connect_error);
}

$student = $_POST['student'] ?? '';
$semester = $_POST['semester'] ?? '';
$courses = $_POST['course'] ?? [];
$credits = $_POST['credits'] ?? [];
$grades  = $_POST['grade'] ?? [];

$totalPoints = 0;
$totalCredits = 0;

for($i=0; $i<count($courses); $i++){
$cr = floatval($credits[$i]);
$gr = floatval($grades[$i]);
if($cr <= 0) continue;

$totalPoints += $cr * $gr;
$totalCredits += $cr;
}

if($totalCredits <= 0){
echo "<div class='alert alert-danger'>No valid courses.</div>";
exit;
}

$gpa = $totalPoints / $totalCredits;

if($gpa >= 3.7){
$status = "Distinction";
$barClass = "bg-success";
}
elseif($gpa >= 3.0){
$status = "Merit";
$barClass = "bg-info";
}
elseif($gpa >= 2.0){
$status = "Pass";
$barClass = "bg-warning";
}
else{
$status = "Fail";
$barClass = "bg-danger";
}

$stmt = $conn->prepare(
"INSERT INTO results(student_name, semester, gpa)
 VALUES(?,?,?)"
);
$stmt->bind_param("ssd", $student, $semester, $gpa);
$stmt->execute();
$stmt->close();

echo "<div class='alert alert-primary'>";
echo "<strong>Student:</strong> ".htmlspecialchars($student)." | ";
echo "<strong>Semester:</strong> ".htmlspecialchars($semester)."<br>";
echo "<strong>GPA:</strong> ".number_format($gpa,2)." ($status)";
echo "</div>";

$percent = ($gpa/4)*100;
echo "
<div class='progress mb-3'>
  <div class='progress-bar $barClass' style='width: {$percent}%'>
    ".number_format($gpa,2)."
  </div>
</div>";

echo "<table class='table table-bordered'>";
echo "<tr>
<th>Course</th>
<th>Credits</th>
<th>Grade</th>
<th>Points</th>
</tr>";

for($i=0; $i<count($courses); $i++){
$course = htmlspecialchars($courses[$i]);
$cr = floatval($credits[$i]);
$gr = floatval($grades[$i]);
if($cr <= 0) continue;

$pts = $cr * $gr;

echo "<tr>
<td>$course</td>
<td>$cr</td>
<td>$gr</td>
<td>$pts</td>
</tr>";
}
echo "</table>";

$res = $conn->query("SELECT student_name, semester, gpa, created_at
                     FROM results ORDER BY id DESC");

echo "<h4>Previous Results</h4>";
echo "<table class='table table-sm table-striped'>";
echo "<tr>
<th>Name</th><th>Semester</th><th>GPA</th><th>Date</th>
</tr>";

while($row = $res->fetch_assoc()){
echo "<tr>
<td>".htmlspecialchars($row['student_name'])."</td>
<td>".htmlspecialchars($row['semester'])."</td>
<td>".number_format($row['gpa'],2)."</td>
<td>".$row['created_at']."</td>
</tr>";
}
echo "</table>";

$conn->close();
$res = $conn->query("SELECT * FROM results ORDER BY id DESC");

echo "<h3>Previous Results</h3>";

while($r=$res->fetch_assoc()){
echo "<p>
$r[student_name] -
$r[semester] -
GPA: $r[gpa]
</p>";
}
?>
