<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// GET -> lista studenți
if ($method === 'GET') {
    $studenti = [];
    $rez = $conn->query("SELECT id, nume, an_studiu, media FROM studenti ORDER BY id DESC");
    if ($rez) {
        while ($row = $rez->fetch_assoc()) {
            $studenti[] = $row;
        }
    }
    echo json_encode(["success" => true, "data" => $studenti]);
    exit;
}

// POST -> adaugă student (primește JSON)
if ($method === 'POST') {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    $nume = trim($data['nume'] ?? '');
    $an   = intval($data['an_studiu'] ?? 0);
    $med  = floatval($data['media'] ?? -1);

    // validare
    if ($nume === '' || $an < 1 || $an > 4 || $med < 0 || $med > 10) {
        echo json_encode(["success" => false, "message" => "Date invalide. Completează nume, an (1-4), media (0-10)."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO studenti (nume, an_studiu, media) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $nume, $an, $med);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student adăugat."]);
    } else {
        echo json_encode(["success" => false, "message" => "Eroare la inserare."]);
    }
    $stmt->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Metodă neacceptată."]);
