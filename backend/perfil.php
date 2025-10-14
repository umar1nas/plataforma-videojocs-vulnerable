<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Perfil de Usuario</title>
  <style>
    :root {
      --bg: #0F172A; /* Fondo oscuro */
      --card-bg: #1E293B; /* Gris azulado oscuro */
      --text-light: #F1F5F9; /* Texto claro */
      --text-muted: #E2E8F0; /* Gris claro */
      --accent-from: #A855F7; /* Morado degradado inicio */
      --accent-to: #7C3AED;   /* Morado degradado final */
      --button: #8B5CF6; /* Violeta suave */
      --highlight: #10B981; /* Verde (ícono de rombo) */
      --pink: #D946EF; /* Rosa/Morado botón */
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      color: var(--text-light);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .card {
      width: 100%;
      max-width: 700px;
      border-radius: 22px;
      overflow: hidden;
      background: var(--card-bg);
      box-shadow: 0 8px 32px rgba(0,0,0,0.45);
      border: 1px solid rgba(255,255,255,0.05);
    }

    .card__hero {
      padding: 40px 20px;
      background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
      color: white;
      text-align: center;
    }

    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255,255,255,0.4);
      margin-bottom: 16px;
    }

    .hero__title { font-size: 24px; margin: 0; font-weight: 600; }
    .hero__sub { font-size: 14px; opacity: 0.9; margin-top: 6px; }

    .card__body { padding: 32px 40px; }
    .field { margin-bottom: 20px; }
    .label { display: block; font-size: 13px; color: var(--text-muted); font-weight: 600; letter-spacing: 0.6px; text-transform: uppercase; }
    .value { margin-top: 6px; font-size: 16px; color: var(--text-light); font-weight: 500; }

    .footer {
      padding: 14px 16px;
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
      background: #111827;
      border-top: 1px solid rgba(255,255,255,0.05);
    }

    .btn-return {
      display: inline-block;
      margin-top: 16px;
      padding: 12px 24px;
      border-radius: 12px;
      background: var(--button);
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.2s ease;
    }

    .btn-return:hover {
      background: var(--accent-to);
    }

    .icon {
      display: inline-block;
      width: 10px;
      height: 10px;
      background: var(--highlight);
      transform: rotate(45deg);
      margin-right: 6px;
    }

    @media (max-width: 700px) {
      .card { border-radius: 18px; }
      .avatar { width: 90px; height: 90px; }
      .card__body { padding: 24px; }
    }
  </style>
</head>
<body>

  <div class="card">
    <div class="card__hero">
      <img src="./fotos/default.png" alt="Foto de usuario" class="avatar" />
      <h1 class="hero__title">maria</h1>
      <div class="hero__sub">Miembro desde: 2024-05-20</div>
    </div>

    <div class="card__body">
      <div class="field">
        <label class="label">Nombre completo</label>
        <div class="value">Maria Gómez</div>
      </div>

      <div class="field">
        <label class="label">Nombre de usuario</label>
        <div class="value">maria</div>
      </div>

      <div class="field">
        <label class="label">Email</label>
        <div class="value">maria@ejemplo.com</div>
      </div>

      <div style="text-align:center;">
        <a href="plataforma.php" class="btn-return"><span class="icon"></span>Volver al menú inicial</a>
      </div>
      <div style="text-align:center;">
        <a href="editar_perfil.php" class="btn-return"><span class="icon"></span>Editar perfil</a>
      </div>
    </div>

    <div class="footer">Diseño minimalista con paleta personalizada</div>
  </div>

</body>
</html>
