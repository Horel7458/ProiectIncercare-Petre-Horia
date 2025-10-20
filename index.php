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
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Intră în lumea pasionaților</h2>

    <!-- Formularul te duce către pagina_principala.php -->
    <form action="pagina_principala.php" method="post">
      <label for="username">Utilizator</label>
      <input type="text" id="username" name="username" placeholder="Introdu numele de utilizator" required>

      <label for="password">Parolă</label>
      <input type="password" id="password" name="password" placeholder="Introdu parola" required>

      <button type="submit">Login</button>

      <div class="signup-link">
        Nu ai cont? <a href="#">Înregistrează-te</a>
      </div>
    </form>
  </div>
</body>
</html>
