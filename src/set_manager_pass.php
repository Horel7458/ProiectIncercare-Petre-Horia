<?php
require 'config.php';

$id_manager = 5;              // schimbi cu managerul dorit
$parolaNoua = "manager123";   // parola pe care vrei s-o pui

$hash = password_hash($parolaNoua, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE manageri SET parola = ? WHERE id_manager = ?");
$stmt->bind_param("si", $hash, $id_manager);

if ($stmt->execute()) {
  echo "Parola setată cu succes pentru managerul ID = $id_manager. Parola: $parolaNoua";
} else {
  echo "Eroare la setare parolă.";
}
$stmt->close();
