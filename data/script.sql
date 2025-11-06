-- SCRIPT PER AUTOMATITZAR LA CREACIÓ DE LA BASE DE DADES DEL PROJECTE

-- Crear la base de dades
CREATE DATABASE plataforma_videojocs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear un usuari insegur per l'aplicació
CREATE USER 'plataforma_user'@'%' IDENTIFIED BY '123456789a';

-- Concedir tots els privilegis a la base de dades i de manera global (vulnerable)
GRANT ALL PRIVILEGES ON *.* TO 'plataforma_user'@'%' WITH GRANT OPTION;

-- Aplicar els canvis
FLUSH PRIVILEGES;

-- Seleccionar la base de dades
USE plataforma_videojocs;

-- Taula d'usuaris
CREATE TABLE IF NOT EXISTS usuaris (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_usuari VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(100),
    foto_perfil VARCHAR(255) DEFAULT './fotos/default.png',
    data_registre DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula de jocs
CREATE TABLE IF NOT EXISTS jocs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_joc VARCHAR(50) NOT NULL,
    descripcio TEXT,
    puntuacio_maxima INT DEFAULT 0,
    nivells_totals INT DEFAULT 1,
    actiu BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula de nivells dels jocs
CREATE TABLE IF NOT EXISTS nivells_joc (
    id INT PRIMARY KEY AUTO_INCREMENT,
    joc_id INT NOT NULL,
    nivell INT NOT NULL,
    nom_nivell VARCHAR(50),
    configuracio_json JSON NOT NULL,
    puntuacio_minima INT DEFAULT 0,
    FOREIGN KEY (joc_id) REFERENCES jocs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula de progrés d'usuari
CREATE TABLE IF NOT EXISTS progres_usuari (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuari_id INT NOT NULL,
    joc_id INT NOT NULL,
    nivell_actual INT DEFAULT 1,
    puntuacio_maxima INT DEFAULT 0,
    partides_jugades INT DEFAULT 0,
    ultima_partida DATETIME,
    FOREIGN KEY (usuari_id) REFERENCES usuaris(id) ON DELETE CASCADE,
    FOREIGN KEY (joc_id) REFERENCES jocs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Taula de partides
CREATE TABLE IF NOT EXISTS partides (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuari_id INT NOT NULL,
    joc_id INT NOT NULL,
    nivell_jugat INT NOT NULL,
    puntuacio_obtinguda INT NOT NULL,
    data_partida DATETIME DEFAULT CURRENT_TIMESTAMP,
    durada_segons INT DEFAULT 0,
    FOREIGN KEY (usuari_id) REFERENCES usuaris(id) ON DELETE CASCADE,
    FOREIGN KEY (joc_id) REFERENCES jocs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Insertar un juego JOC 1
INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
VALUES 
(1, 'Las navecitas', 'Joc de acció on el jugador controla una nau que ha de evitar asteroides i derrotar enemics per superar nivells.', 1000, 5, TRUE);

-- Insertar niveles para "Las navecitas"

INSERT INTO nivells_joc (joc_id, nivell, nom_nivell, configuracio_json, puntuacio_minima)
VALUES
(1, 1, 'Nivell 1', JSON_OBJECT(
  'vides', 3,
  'puntsNivell', 100,
  'maxEnemics', 5,
  'maxAsteroides', 30
), 100),

(1, 2, 'Nivell 2', JSON_OBJECT(
  'vides', 3,
  'puntsNivell', 200,
  'maxEnemics', 8,
  'maxAsteroides', 40
), 200),

(1, 3, 'Nivell 3', JSON_OBJECT(
  'vides', 3,
  'puntsNivell', 300,
  'maxEnemics', 10,
  'maxAsteroides', 60
), 300),

(1, 4, 'Nivell 4', JSON_OBJECT(
  'vides', 2,
  'puntsNivell', 400,
  'maxEnemics', 12,
  'maxAsteroides', 80
), 400),

(1, 5, 'Nivell 5', JSON_OBJECT(
  'vides', 1,
  'puntsNivell', 500,
  'maxEnemics', 15,
  'maxAsteroides', 100
), 500);


-- Insertar un juego JOC 2


INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
VALUES 
(2, 'Pong', 'El joc del PONG', 1000, 5, TRUE);



-- Insertar un juego JOC 3

INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
VALUES 
(3, 'Snake', 'El joc del snake', 1000, 5, TRUE);



-- Insertar un juego JOC 4
 INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
  VALUES (
   4,
  'Flappy Bird',
  'El clásico juego donde el pájaro debe esquivar los obstáculos.',
   1000,
   5,
   TRUE 
);


INSERT INTO nivells_joc (joc_id, nivell, nom_nivell, configuracio_json, puntuacio_minima)
VALUES
(4, 1, 'Nivel 1', JSON_OBJECT('velocidad', 5, 'obstaculos', 10), 10),
(4, 2, 'Nivel 2', JSON_OBJECT('velocidad', 6, 'obstaculos', 20), 20),
(4, 3, 'Nivel 3', JSON_OBJECT('velocidad', 7, 'obstaculos', 30), 30),
(4, 4, 'Nivel 4', JSON_OBJECT('velocidad', 8, 'obstaculos', 40), 40),
(4, 5, 'Nivel 5', JSON_OBJECT('velocidad', 9, 'obstaculos', 50), 50);




-- Insertar un juego JOC 5

INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
VALUES 
(5, '2048', 'El 2048', 1000, 5, TRUE);




-- Insertar un juego JOC 6

INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
VALUES 
(6, 'Shoot', 'El joc del shoot shoot shoot', 1000, 5, TRUE);