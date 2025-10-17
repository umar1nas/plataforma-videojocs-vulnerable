<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Error al iniciar sessió - URYAPLAY</title>

  <!-- redirige a login.html pasados 6 segundos -->
  <meta http-equiv="refresh" content="4;url=index.php">

  <style>
    :root{
      --bg:#0F172A;
      --bg-grad:radial-gradient(circle at top right, rgba(124,58,237,0.18), transparent 60%);
      --card-bg:#1E293B;
      --text-light:#F1F5F9;
      --text-muted:#E2E8F0;
      --accent-from:#A855F7;
      --accent-to:#7C3AED;
      --button:#8B5CF6;
      --green:#10B981;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      min-height:100vh;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:var(--text-light);
      background:var(--bg);
      background-image:var(--bg-grad);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:28px;
    }

    .card{
      width:100%;
      max-width:520px;
      border-radius:18px;
      overflow:hidden;
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)), var(--card-bg);
      box-shadow: 0 14px 40px rgba(2,6,23,0.6);
      border:1px solid rgba(255,255,255,0.04);
    }

    .card__hero{
      padding:20px 22px;
      background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
      color:white;
      display:flex;
      align-items:center;
      justify-content:center; /* centrado horizontal */
      gap:14px;
    }

    .logo{
      width:56px;
      height:56px;
      border-radius:50%;
      background:var(--button);
      display:grid;
      place-items:center;
      font-size:26px;
      color:white;
      box-shadow: 0 8px 22px rgba(139,92,246,0.25);
    }

    .hero__title{
      margin:0;
      font-size:16px;
      font-weight:700;
      letter-spacing:0.6px;
      text-transform:uppercase;
      display:block;
      margin-left:10px;
    }

    .card__body{
      padding:28px 26px;
      text-align:center;
    }

    h1{
      margin:6px 0 8px;
      font-size:20px;
      color:var(--text-light);
    }

    h2{
      margin:0 0 10px;
      font-size:15px;
      color:var(--text-muted);
      font-weight:600;
    }

    .note {
      margin-top:12px;
      color:var(--text-muted);
      font-size:13px;
    }

    footer{
      padding:12px 16px;
      text-align:center;
      font-size:12px;
      color:var(--text-muted);
      background:#0b1220;
      border-top:1px solid rgba(255,255,255,0.02);
    }

    @media (max-width:520px){ .card{border-radius:14px} }
  </style>
</head>
<body>

  <div class="card" role="alert" aria-live="assertive">
    <div class="card__hero">
      <div class="logo">🎮</div>
      <!-- si no quieres texto en la cabecera, borra la línea siguiente -->
      <div class="hero__title">URYAPLAY</div>
    </div>

    <div class="card__body">
      <h1>Error al inicia sessió</h1>
      <h2>usuari no trobat o dades incorrectes</h2>

      <p class="note">Seràs redirigit a la pàgina d'inici d'aquí 6 segons.</p>
    </div>

    <footer>Plataforma de videojocs — URYAPLAY</footer>
  </div>

</body>
</html>
