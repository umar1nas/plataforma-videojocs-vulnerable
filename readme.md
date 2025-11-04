🚀 LAMP  —  Docker 

README completo y visual para levantar un entorno LAMP aislado con Docker Compose.

🎯 Objetivo

Levantar rápidamente un entorno LAMP (Apache + PHP + MySQL) usando Docker Compose, con una carpeta www/. Listo para arrancar plataforma de juegos

📁 Estructura del proyecto
/lamp
│
├─ docker-compose.yml
├─ php.ini           # 
├─ www/              #
│   └─ script.php    # script plataforma de juegos
└─ README.md         # este archivo
🧩 ¿Por qué usar Docker?

Aislamiento: no tocas tu sistema base.

Fácil de levantar y eliminar (up / down -v).

Reproducible: funciona igual en cualquier máquina con Docker.

📦 docker-compose.yml (coloca este en la raíz)
version: '3.8'
services:
  web:
    image: php:8.1-apache
    container_name: lamp_web
    volumes:
      - ./www:/var/www/html:delegated
      - ./php.ini:/usr/local/etc/php/php.ini
    ports:
      - "8080:80"
    depends_on:
      - db


  db:
    image: mysql:5.7
    container_name: lamp_db
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: gamedb
      MYSQL_USER: gamer
      MYSQL_PASSWORD: gamerpass
    volumes:
      - dbdata:/var/lib/mysql


  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: lamp_pma
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: rootpass
    ports:
      - "8081:80"


volumes:
  dbdata:
🛠️ Paso a paso (rápido)

Instala Docker y Docker Compose si no los tienes.

Clona este repo o crea la estructura mostrada arriba.

Crea la carpeta www/ si no existe: mkdir -p www.

Dentro de www/ encontrarás script.php con contenido para desplegar plataforma de juegos 

Levanta el stack:

docker compose up -d

Accede en el navegador:

Web: http://localhost:8080/

phpMyAdmin: http://localhost:8081/ (usuario: root, contraseña: rootpass)

Para parar y limpiar:

docker compose down -v
📎 Archivo: www/script.php

------------ Ejecutar script
<?php
SCRIPT
</html>

🧰 Comandos útiles

Ver contenedores: docker compose ps

Logs web: docker logs -f lamp_web

Acceder al contenedor web: docker exec -it lamp_web bash

Eliminar contenedores y volúmenes: docker compose down -v
