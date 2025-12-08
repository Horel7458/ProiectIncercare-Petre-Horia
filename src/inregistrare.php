<?php
require 'config.php'; // conexiune la baza de date

$eroare = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $nume_complet = trim($_POST['nume_complet'] ?? '');
    $parola       = trim($_POST['parola'] ?? '');
    $parola2      = trim($_POST['parola2'] ?? '');

    // Validări simple
    if ($username === '' || $email === '' || $parola === '' || $parola2 === '') {
        $eroare = 'Te rugăm să completezi toate câmpurile obligatorii.';
    } elseif ($parola !== $parola2) {
        $eroare = 'Parolele nu coincid.';
    } elseif (strlen($parola) < 4) {
        $eroare = 'Parola trebuie să aibă cel puțin 4 caractere.';
    } else {
        // Verificăm dacă există deja username sau email în DB
        $stmt = $conn->prepare("SELECT id_utilizator FROM utilizatori WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $rez = $stmt->get_result();

        if ($rez && $rez->num_rows > 0) {
            $eroare = 'Există deja un cont cu acest nume de utilizator sau email.';
        } else {
            // Totul ok -> inserăm utilizatorul
            $parola_hash = password_hash($parola, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("
                INSERT INTO utilizatori (username, email, parola, nume_complet)
                VALUES (?, ?, ?, ?)
            ");
            $stmt_insert->bind_param("ssss", $username, $email, $parola_hash, $nume_complet);

            if ($stmt_insert->execute()) {
                $succes = 'Cont creat cu succes! Poți merge la pagina de login.';
            } else {
                $eroare = 'A apărut o eroare la salvarea datelor.';
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Înregistrare - Parc Auto</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('audi.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: rgba(0, 0, 0, 0.7);
      padding: 40px;
      border-radius: 15px;
      color: white;
      width: 340px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display:block;
      text-align:left;
      margin-bottom:4px;
      font-size:14px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 6px 0 14px 0;
      border: none;
      border-radius: 5px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 5px;
    }

    button:hover {
      background-color: #0056b3;
    }

    .bottom-link {
      text-align: center;
      margin-top: 12px;
      font-size: 14px;
    }

    .bottom-link a {
      color: #00bfff;
      text-decoration: none;
    }

    .bottom-link a:hover {
      text-decoration: underline;
    }

    .msg {
      font-size: 13px;
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 6px;
    }
    .msg.error {
      background: rgba(220, 38, 38, 0.2);
      border: 1px solid #f87171;
    }
    .msg.success {
      background: rgba(22, 163, 74, 0.2);
      border: 1px solid #4ade80;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Înregistrare cont nou</h2>

    <?php if ($eroare): ?>
      <div class="msg error"><?= htmlspecialchars($eroare) ?></div>
    <?php endif; ?>

    <?php if ($succes): ?>
      <div class="msg success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <form method="post" action="inregistrare.php">
      <label for="username">Utilizator *</label>
      <input type="text" id="username" name="username" required
             value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">

      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required
             value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

      <label for="nume_complet">Nume complet</label>
      <input type="text" id="nume_complet" name="nume_complet"
             value="<?= isset($nume_complet) ? htmlspecialchars($nume_complet) : '' ?>">

      <label for="parola">Parolă *</label>
      <input type="password" id="parola" name="parola" required>

      <label for="parola2">Confirmă parola *</label>
      <input type="password" id="parola2" name="parola2" required>

      <button type="submit">Creează cont</button>

      <div class="bottom-link">
        Ai deja cont? <a href="index.php">Mergi la login</a>

      </div>
    </form>
  </div>
</body>
</html>
