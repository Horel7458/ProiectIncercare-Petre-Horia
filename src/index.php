<?php
session_start();
require 'config.php';

$eroare = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $parola   = trim($_POST['password'] ?? '');

    if ($username === '' || $parola === '') {
        $eroare = "Completează toate câmpurile.";
    } else {
        // căutăm utilizatorul în baza de date
        $stmt = $conn->prepare("SELECT id_utilizator, parola FROM utilizatori WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $rez = $stmt->get_result();

        if ($rez && $rez->num_rows === 1) {
            $user = $rez->fetch_assoc();

            // verificare parolă (hash)
            if (password_verify($parola, $user['parola'])) {
                // LOGIN REUȘIT ✔
                $_SESSION['logat'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['id_utilizator'] = $user['id_utilizator'];

                header("Location: pagina_principala.php");
                exit;
            } else {
                $eroare = "Parola este greșită!";
            }
        } else {
            $eroare = "Utilizatorul nu există!";
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
  <title>Login - Parc Auto</title>
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
      width: 320px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    input[type="text"], input[type="password"] {
      width: 100%;   
      padding: 10px;
      margin: 8px 0 20px 0;
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
    }

    button:hover {
      background-color: #0056b3;
    }

    .signup-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .signup-link a {
      color: #00bfff;
      text-decoration: none;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .eroare {
      background: rgba(255, 0, 0, 0.2);
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid red;
      text-align:center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Intră în lumea pasionaților</h2>

    <?php if ($eroare): ?>
      <div class="eroare"><?= htmlspecialchars($eroare) ?></div>
    <?php endif; ?>

    <form action="index.php" method="post">
      <label for="username">Utilizator</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Parolă</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Login</button>

      <div class="signup-link">
  Nu ai cont? <a href="inregistrare.php">Înregistrează-te</a>
</div>

<div style="text-align:center; margin-top:10px;">
  <a href="manager_login.php" style="color:#00bfff; text-decoration:none; font-size:14px;">
    Intrare Manager
  </a>
</div>
    </form>
  </div>
</body>
</html>
