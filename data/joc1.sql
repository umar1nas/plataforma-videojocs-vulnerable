USE plataforma_videojocs;


-- Insertar un juego
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
