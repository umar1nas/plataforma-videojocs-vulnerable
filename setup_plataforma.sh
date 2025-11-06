#!/usr/bin/env bash
set -euo pipefail

# Script para instalar Apache2 + PHP + MariaDB, clonar repo y
# importar la BBDD desde data/script.sql
#
# Probado en Debian/Ubuntu (apt). Si usas otra distro modifica el gestor de paquetes.
#
# Uso: guardar como setup_plataforma.sh, chmod +x y sudo ./setup_plataforma.sh

REPO="https://github.com/umar1nas/plataforma-videojocs-vulnerable.git"
APP_DIR="/var/www/html/projecte"
SQL_REL_PATH="data/script.sql"   # ruta dentro del repo
APACHE_SITE_CONF="/etc/apache2/sites-available/plataforma.conf"
DB_IMPORT_AS_ROOT=true           # true: importa usando 'sudo mysql < file'

# Paquetes a instalar
PACKAGES=(
  apache2
  mariadb-server
  git
  php
  libapache2-mod-php
  php-mysql
  php-xml
  php-mbstring
  php-zip
  php-cli
  php-curl
  unzip
)

echo "=== Actualizando repositorios e instalando paquetes necesarios ==="
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y "${PACKAGES[@]}"

echo "=== Habilitando e iniciando servicios ==="
systemctl enable apache2
systemctl start apache2
systemctl enable mariadb
systemctl start mariadb

echo "=== Clonando el repositorio ==="
if [ -d "$APP_DIR" ]; then
  echo "Directorio $APP_DIR ya existe. Actualizando repo (git pull)..."
  git -C "$APP_DIR" pull --rebase || true
else
  git clone "$REPO" "$APP_DIR"
fi

# Verificar existencia del SQL
SQL_PATH="$APP_DIR/$SQL_REL_PATH"
if [ ! -f "$SQL_PATH" ]; then
  echo "ERROR: no encuentro el fichero SQL en $SQL_PATH"
  exit 1
fi

echo "=== Importando la base de datos desde $SQL_PATH ==="
# Si MariaDB está configurado con auth_socket, 'sudo mysql' permitirá ejecutar como root sin contraseña.
if $DB_IMPORT_AS_ROOT; then
  echo "Importando usando 'sudo mysql < file' (se ejecuta como root en el servidor de base de datos)..."
  sudo mysql < "$SQL_PATH"
else
  # Si prefieres crear DB y usuario manualmente puedes descomentar/usar el bloque siguiente.
  # mysql -u root -p < "$SQL_PATH"
  echo "IMPORT_SWITCH disabled, no se importó la base de datos."
fi

echo "=== Ajustando permisos de ficheros web ==="
# Poner propietario www-data para que Apache pueda servirlo
chown -R www-data:www-data "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;

echo "=== Configurando sitio Apache ==="
# Crear configuración de sitio si no existe - apunta DocumentRoot al repo
if [ ! -f "$APACHE_SITE_CONF" ]; then
  cat > "$APACHE_SITE_CONF" <<EOF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot $APP_DIR

    <Directory $APP_DIR>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/plataforma_error.log
    CustomLog \${APACHE_LOG_DIR}/plataforma_access.log combined
</VirtualHost>
EOF
  a2ensite plataforma.conf
else
  echo "El fichero $APACHE_SITE_CONF ya existe. No lo sobrescribo."
fi

# Habilitar mod_rewrite por si la app lo necesita
a2enmod rewrite || true

echo "=== Reiniciando Apache para aplicar cambios ==="
systemctl reload apache2

echo "=== Resultado / comprobaciones rápidas ==="
echo " - Código servido desde: $APP_DIR"
echo " - SQL importado desde: $SQL_PATH"
echo " - Apache site: plataforma (enabled)"
echo ""
echo "Puedes comprobar logs de Apache:"
echo "  sudo tail -n 80 /var/log/apache2/plataforma_error.log"
echo ""
echo "Si necesitas acceder a la base de datos como root (sin contraseña), usa:"
echo "  sudo mysql"
echo ""
echo "Si la importación falló por privilegios, intenta ejecutar manualmente:"
echo "  sudo mysql < $SQL_PATH"
echo ""
echo "Finalizado. Accede desde http://<tu-ip> o http://localhost"
