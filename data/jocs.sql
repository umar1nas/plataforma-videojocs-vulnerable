USE plataforma_videojocs;


-- -- Insertar un juego
-- INSERT INTO jocs (nom_joc, descripcio, puntuacio_maxima, nivells_totals)
-- VALUES ('Aventura Màgica', 'Juego de aventuras con varios niveles mágicos', 1000, 3);

-- -- Insertar un nivel para ese juego
-- INSERT INTO nivells_joc (joc_id, nivell, nom_nivell, configuracio_json, puntuacio_minima)
-- VALUES (
--     1, -- ID del juego recién insertado
--     1, -- nivel 1
--     'Bosque Encantado', -- nombre del nivel
--     JSON_OBJECT('enemics', 5, 'temps_limit', 300), -- configuración en JSON
--     100 -- puntuación mínima
-- );



I-- INSERT INTO jocs (id, nom_joc, descripcio, puntuacio_maxima, nivells_totals, actiu)
-- VALUES (
--    4,
 --   'Flappy Bird',
 --   'El clásico juego donde el pájaro debe esquivar los obstáculos.',
 --   1000,
 --   5,
 --   TRUE 
--  );

-- Insertar niveles de Flappy Bird

INSERT INTO nivells_joc (joc_id, nivell, nom_nivell, configuracio_json, puntuacio_minima)
VALUES
(4, 1, 'Nivel 1', JSON_OBJECT('velocidad', 5, 'obstaculos', 10), 10),
(4, 2, 'Nivel 2', JSON_OBJECT('velocidad', 6, 'obstaculos', 20), 20),
(4, 3, 'Nivel 3', JSON_OBJECT('velocidad', 7, 'obstaculos', 30), 30),
(4, 4, 'Nivel 4', JSON_OBJECT('velocidad', 8, 'obstaculos', 40), 40),
(4, 5, 'Nivel 5', JSON_OBJECT('velocidad', 9, 'obstaculos', 50), 50);




