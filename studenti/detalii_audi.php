<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Audi RS7 – Detalii</title>
  <style>
    body { margin:0; font-family:Arial, sans-serif; color:#fff; background: linear-gradient(135deg, #0f0f10, #20232a); }
    .wrap { max-width: 1000px; margin: 0 auto; padding: 30px; text-align:center; }
    .card { background: rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; }
    img { width:100%; max-height:420px; object-fit:cover; border-radius:12px; }
    h1 { margin: 10px 0 5px; }
    .price { font-size: 22px; margin: 8px 0 12px; color:#7fd1ff; }
    .desc { max-width: 800px; margin: 0 auto 14px; line-height:1.6; opacity:.95; }
    ul { text-align:left; max-width:700px; margin:14px auto; line-height:1.7; }
    .dealer { margin-top:16px; background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; }
    .dealer h2 { margin:0 0 8px; font-size:20px; color:#7fd1ff; }
    .actions { margin-top: 18px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
    .btn { border:none; padding:12px 18px; border-radius:8px; cursor:pointer; }
    .primary { background:#007bff; color:#fff; }
    .primary:hover { background:#0056b3; }
    .ghost { background:transparent; color:#7fd1ff; border:1px solid #7fd1ff; }
    .ghost:hover { background: rgba(127,209,255,0.15); }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <img src="audi.jpg" alt="Audi RS7">
      <h1>Audi RS7</h1>
      <div class="price">Preț: 139.900 €</div>
      <p class="desc">Audi RS7 îmbină confortul unui grand tourer cu performanța pură: V8 biturbo, interior premium și tehnologie avansată pentru condusul de zi cu zi și escapade dinamice.</p>
      <ul>
        <li>Motor: 4.0 V8 biturbo</li>
        <li>Putere: 600 CP</li>
        <li>0–100 km/h: 3.6 s</li>
        <li>Tracțiune: Quattro</li>
        <li>Cutie: automată 8 trepte</li>
        <li>An fabricație: 2022</li>
      </ul>

      <div class="dealer">
        <h2>Parc auto: AutoPark Titan</h2>
        <p>Disponibil în parcul nostru din zona Titan (București). Verificare tehnică și istoric transparent.</p>
        <button class="btn ghost" onclick="window.location.href='parc_auto.html'">Accesează parcul auto</button>
      </div>

      <div class="actions">
        <button class="btn primary" onclick="alert('Îți mulțumim! Te vom contacta în curând.')">Contactează-ne</button>
      <button class="btn ghost" onclick="window.location.href='parc_titan.html'">Accesează parcul auto</button>
      </div>
    </div>
  </div>
</body>
</html>
