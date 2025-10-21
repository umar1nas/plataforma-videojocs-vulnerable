<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Editar perfil - NovaPlay</title>
  <style>
    :root {
      --bg: #0F172A;
      --bg-grad: radial-gradient(circle at top right, rgba(124, 58, 237, 0.25), transparent 60%);
      --card-bg: #1E293B;
      --text-light: #F1F5F9;
      --text-muted: #E2E8F0;
      --accent-from: #A855F7;
      --accent-to: #7C3AED;
      --button: #8B5CF6;
      --highlight: #10B981;
      --pink: #D946EF;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      background-image: var(--bg-grad);
      color: var(--text-light);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .card {
      width: 100%;
      max-width: 500px;
      border-radius: 22px;
      overflow: hidden;
      background: var(--card-bg);
      box-shadow: 0 8px 32px rgba(0,0,0,0.45);
      border: 1px solid rgba(255,255,255,0.05);
    }

    .card__hero {
      padding: 32px 20px;
      background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    .logo {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--button);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      color: white;
      box-shadow: 0 0 20px rgba(168, 85, 247, 0.6);
    }

    .platform-name {
      font-size: 20px;
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .hero__title {
      font-size: 22px;
      font-weight: 600;
      margin: 8px 0 0;
    }

    form {
      padding: 32px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    label {
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      display: block;
      margin-bottom: 6px;
    }

    input {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,0.08);
      background: #0F172A;
      color: var(--text-light);
      font-size: 15px;
    }

    input[type="file"] {
      background: none;
      border: none;
      color: var(--text-muted);
    }

    input:focus {
      outline: none;
      border-color: var(--accent-to);
      box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.3);
    }

    .btn {
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      border: none;
      background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
      color: white;
      font-weight: 600;
      cursor: pointer;
      font-size: 16px;
      transition: opacity 0.2s ease;
    }

    .btn:hover {
      opacity: 0.9;
    }

    .avatar-preview {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }

    .avatar-preview img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      border: 3px solid var(--accent-to);
      object-fit: cover;
    }

    .footer {
      text-align: center;
      font-size: 14px;
      color: var(--text-muted);
      padding: 20px;
      border-top: 1px solid rgba(255,255,255,0.05);
    }

    .link {
      color: var(--pink);
      text-decoration: none;
      font-weight: 600;
    }

    .link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="card">
    <div class="card__hero">
      <div class="logo">🎮</div>
      <h1 class="hero__title">Editar perfil</h1>
    </div>

    <form action="modificar_perfil.php" method="post" enctype="multipart/form-data">
      <div class="avatar-preview">
        <img id="avatarImg" src="./fotos/default.png" alt="Avatar del usuario">
        <label for="avatar" style="font-size:13px;color:var(--text-muted)">Cambiar imagen</label>
        <input type="file" id="avatar" name="avatar" accept="fotos/">
      </div>

      <div>
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" >
      </div>

      <div>
        <label for="usuario">Nombre de usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" >
      </div>

      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" >
      </div>

       <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Nueva contraseña" >
      </div>

      <button type="submit" class="btn">Guardar cambios</button>
    </form>

    <div class="footer">
      <a href="perfil.php" class="link">Volver al perfil</a>
    </div>
  </div>

</body>
</html>
