<?php
// adaptează user/parola/host la ce ai în docker-compose
$host = "db";          // de obicei "db" în Docker sau "localhost" în afara lui
$user = "admin";        // sau userul tău
$pass = "horia";        // parola ta
$db   = "proiecttw";   // baza de date creată

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Eroare conexiune: " . $conn->connect_error);
}
?>
