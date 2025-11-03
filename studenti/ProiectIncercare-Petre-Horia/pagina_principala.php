<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parc Auto - Pagina Principală</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('audi.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      text-align: center;
      min-height: 100vh;
    }
    .content {
      background: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 15px;
      display: inline-block;
      margin-top: 40px;
      max-width: 1200px;
    }
    h1 { margin-bottom: 20px; }
    .cars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .car-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 15px;
      transition: transform 0.3s, background 0.3s;
    }
    .car-card:hover {
      transform: scale(1.05);
      background: rgba(255, 255, 255, 0.2);
    }
    .car-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
    }
    .car-card h3 { margin: 10px 0; }
    .car-card button {
      background-color: #007bff;
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .car-card button:hover { background-color: #0056b3; }
    .logout { margin-top: 25px; }
    .logout a {
      color: #00bfff; text-decoration: none; font-weight: bold;
    }
    .logout a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="content">
    <h1>Bun venit în parcul auto!</h1>
    <p>Alege una dintre mașinile disponibile pentru vânzare:</p>

    <div class="cars-grid">
      <div class="car-card">
        <img src="audi.jpg" alt="Audi RS6">
        <h3>Audi RS6</h3>
        <button onclick="window.location.href='detalii_audi.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="bmw.jpg" alt="BMW M4">
        <h3>BMW M4 Competition</h3>
        <button onclick="window.location.href='detalii_bmw.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="mercedes.jpg" alt="Mercedes AMG">
        <h3>Mercedes-AMG GT</h3>
        <button onclick="window.location.href='detalii_mercedes.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="porsche.jpg" alt="Porsche 911">
        <h3>Porsche 911 Turbo S</h3>
        <button onclick="window.location.href='detalii_porsche.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="lamborghini.jpg" alt="Lamborghini Huracán">
        <h3>Lamborghini Huracán</h3>
        <button onclick="window.location.href='detalii_lamborghini.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="ferrari.jpg" alt="Ferrari F8">
        <h3>Ferrari F8 Tributo</h3>
        <button onclick="window.location.href='detalii_ferrari.php'">Vezi detalii</button>
      </div>
    </div>

    <div class="logout">
      <a href="login.php">Ieși din cont</a>
    </div>
  </div>
</body>
</html>
