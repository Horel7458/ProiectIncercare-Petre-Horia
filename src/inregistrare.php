<?php
require 'config.php';

$eroare = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $nume_complet = trim($_POST['nume_complet'] ?? '');
    $parola       = $_POST['parola'] ?? '';
    $parola2      = $_POST['parola2'] ?? '';

    if ($username === '' || $email === '' || $parola === '' || $parola2 === '') {
        $eroare = "Completează toate câmpurile obligatorii.";
    } elseif (strlen($parola) < 6) {
        $eroare = "Parola trebuie să aibă minim 6 caractere.";
    } elseif ($parola !== $parola2) {
        $eroare = "Parolele nu coincid.";
    } else {
        $stmt = $conn->prepare("SELECT id_utilizator FROM utilizatori WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $rez = $stmt->get_result();

        if ($rez->num_rows > 0) {
            $eroare = "Există deja un cont cu acest username sau email.";
        } else {
            $hash = password_hash($parola, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare("
                INSERT INTO utilizatori (username, email, parola, nume_complet)
                VALUES (?, ?, ?, ?)
            ");
            $stmt2->bind_param("ssss", $username, $email, $hash, $nume_complet);

            if ($stmt2->execute()) {
                $succes = "Cont creat cu succes! Te poți autentifica.";
            } else {
                $eroare = "Eroare la salvarea contului.";
            }
            $stmt2->close();
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
<title>Înregistrare</title>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: linear-gradient(135deg,#0f172a,#020617);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}

.card {
  width: 360px;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(8px);
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0,0,0,.6);
}

.card h2 {
  text-align: center;
  margin-bottom: 20px;
}

label {
  font-size: 13px;
  display: block;
  margin-bottom: 4px;
}

input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: none;
  margin-bottom: 14px;
  box-sizing: border-box;
}

input:focus {
  outline: 2px solid #38bdf8;
}

button {
  width: 100%;
  padding: 11px;
  background: linear-gradient(135deg,#38bdf8,#2563eb);
  border: none;
  color: white;
  font-weight: bold;
  border-radius: 10px;
  cursor: pointer;
}

button:hover {
  opacity: 0.9;
}

.msg {
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 15px;
  font-size: 13px;
  text-align: center;
}

.error {
  background: rgba(220,38,38,.25);
  border: 1px solid #f87171;
}

.success {
  background: rgba(22,163,74,.25);
  border: 1px solid #4ade80;
}

.small {
  font-size: 12px;
  color: #fca5a5;
  margin-top: -10px;
  margin-bottom: 10px;
}

.link {
  text-align: center;
  margin-top: 14px;
  font-size: 14px;
}

.link a {
  color: #38bdf8;
  text-decoration: none;
}

.link a:hover {
  text-decoration: underline;
}
</style>
</head>

<body>

<div class="card">
  <h2>Creare cont</h2>

  <?php if ($eroare): ?>
    <div class="msg error"><?= htmlspecialchars($eroare) ?></div>
  <?php endif; ?>

  <?php if ($succes): ?>
    <div class="msg success"><?= htmlspecialchars($succes) ?></div>
  <?php endif; ?>

  <form method="post" onsubmit="return validareParole();">

    <label>Utilizator *</label>
    <input type="text" name="username" required>

    <label>Email *</label>
    <input type="email" name="email" required>

    <label>Nume complet</label>
    <input type="text" name="nume_complet">

    <label>Parolă *</label>
    <input type="password" id="parola" name="parola" required>
    <div id="errParola" class="small"></div>

    <label>Confirmă parola *</label>
    <input type="password" id="parola2" name="parola2" required>
    <div id="errParola2" class="small"></div>

    <button type="submit">Creează cont</button>

    <div class="link">
      Ai deja cont? <a href="index.php">Mergi la login</a>
    </div>
  </form>
</div>

<script>
function validareParole() {
  const p1 = document.getElementById("parola").value;
  const p2 = document.getElementById("parola2").value;
  let ok = true;

  document.getElementById("errParola").textContent = "";
  document.getElementById("errParola2").textContent = "";

  if (p1.length < 6) {
    document.getElementById("errParola").textContent =
      "Parola trebuie să aibă minim 6 caractere.";
    ok = false;
  }

  if (p1 !== p2) {
    document.getElementById("errParola2").textContent =
      "Parolele nu coincid.";
    ok = false;
  }

  return ok;
}
</script>

</body>
</html>
