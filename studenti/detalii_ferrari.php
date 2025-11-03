<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ferrari F8 Tributo – Detalii</title>
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
      <img src="ferrari.jpg" alt="Ferrari F8 Tributo">
      <h1>Ferrari F8 Tributo</h1>
      <div class="price">Preț: 259.000 €</div>
      <p class="desc">F8 Tributo este esența performanței Ferrari: motor V8 multiplu premiat, aerodinamică sofisticată și un cockpit orientat către pilot.</p>
      <ul>
        <li>Motor: 3.9 V8 twin-turbo</li>
        <li>Putere: 720 CP</li>
        <li>0–100 km/h: 2.9 s</li>
        <li>Tracțiune: RWD</li>
        <li>Cutie: automată 7 trepte</li>
        <li>An fabricație: 2021</li>
      </ul>

      <div class="dealer">
        <h2>Parc auto: AutoPark Constanța</h2>
        <p>Showroom premium în Constanța, aproape de stațiunea Mamaia. Transport și înmatriculare la cheie.</p>
      <button class="btn ghost" onclick="window.location.href='parc_constanta.html'">Accesează parcul auto</button>
      </div>

      <div class="actions">
        <button class="btn primary" onclick="alert('Îți mulțumim! Te vom contacta în curând.')">Contactează-ne</button>
        <button class="btn ghost" onclick="window.location.href='parc_constanta.html'">Accesează parcul auto</button>
      </div>
    </div>
  </div>
</body>
</html>
