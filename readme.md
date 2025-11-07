# Instalación rápida — Plataforma videojuegos vulnerable

Este README explica **rápidamente** cómo descargar y ejecutar el script `setup_plataforma.sh`, dar permisos, arrancarlo con `sudo` y cómo configurar la IP del servicio en `config.php` (variable `$BASE_IP`).

> **URL del script (raw):**
> `https://raw.githubusercontent.com/umar1nas/plataforma-videojocs-vulnerable/refs/heads/main/setup_plataforma.sh`

---

## 1) Descargar el script (wget o curl)

Con `wget`:

```bash
wget -O setup_plataforma.sh "https://raw.githubusercontent.com/umar1nas/plataforma-videojocs-vulnerable/refs/heads/main/setup_plataforma.sh"
```

Con `curl`:

```bash
curl -L -o setup_plataforma.sh "https://raw.githubusercontent.com/umar1nas/plataforma-videojocs-vulnerable/refs/heads/main/setup_plataforma.sh"
```

---

## 2) Dar permisos de ejecución

```bash
chmod +x setup_plataforma.sh
```

---

## 3) Ejecutar (usar **bash** y **sudo**)

Para evitar errores con `/bin/sh` (p. ej. `pipefail`), ejecuta con `bash`:

```bash
sudo bash ./setup_plataforma.sh
```

El script instalará Apache2, PHP, MariaDB, clonará el repositorio y **tendrá como ruta por defecto**:
`/var/www/html/projecte`

---

## 4) Configurar la IP del servicio en `config.php`

Después de la instalación, edita el fichero `config.php` dentro del repo clonado para establecer la IP en la variable `$BASE_IP`.

Ruta (ejemplo):

```
/var/www/html/rojecte/config.php
```

Abrir con `nano`:

```bash
sudo nano /var/www/projecte/config.php
```

Busca la línea que contiene `$BASE_IP` y pon tu IP entre comillas, por ejemplo:

```php
$BASE_IP = '192.168.1.10';
```

Guarda y cierra (en `nano`: `Ctrl+O` luego `Enter`, `Ctrl+X`).

---

## 5) Comprobación final

* Accede desde tu navegador: `http://<IP-del-servidor>/` o `http://localhost` si trabajas localmente.


## Notas / consejos rápidos

* Asegúrate de ejecutar el script con `bash` (no con `sh`) para evitar el error `set: Illegal option -o pipefail`.

