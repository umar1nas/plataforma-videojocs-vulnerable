// --------- Pantalla del Joc ---------
const pantalla = document.querySelector("#pantalla");
const infoPartida = document.querySelector("#infoPartida");
const pantallaAmple = window.innerWidth;
const pantallaAlt = window.innerHeight;
const fotogrames = 1000 / 60;

// --------- Variables de configuració inicial ---------
const jocId = window.jocConfig.jocId;
const nivell = window.jocConfig.nivell;
//const nomUsuari = "Pepeito";
const nomUsuari = window.jocConfig.nomUsuari;

// Cridem a la API amb les dades reals
fetch(`http://172.18.33.249/projecte/backend/api.php?jocs=${jocId}&nivells=${nivell}`)
  .then(res => res.json())
  .then(data => {
    console.log("Resposta API:", data);

    // Variables del joc des de la API
    const vides = data.vides;
    const maxPunts = data.puntsNivell;
    const maxEnemics = data.maxEnemics;
    const maxAsteroides = data.maxAsteroides || 100;

    // ---- Inicialització de vectors ----
    const vectorAsteroides = [];
    const vectorEnemics = [];

    // ---- Objecte Jugador ----
    const jugador = new Jugador(nomUsuari, vides, 15, {x: 100, y: 300}, 150, 100);
    pantalla.append(jugador.elementHTML);

    // ---- Enemics ----
    for (let i = 0; i < maxEnemics; i++) {
      let posX = pantallaAmple + 50;
      let posY = Math.floor(Math.random() * (pantallaAlt - 50));
      let velocitat = Math.floor(Math.random() * 5) + 1;
      vectorEnemics.push(new Enemic(jugador, velocitat, {x: posX, y: posY}, 50, 50));
      pantalla.append(vectorEnemics[i].elementHTML);
    }

    // ---- Asteroides ----
    for (let i = 0; i < maxAsteroides; i++) {
      let posX = Math.floor(Math.random() * pantallaAmple - 3);
      let posY = Math.floor(Math.random() * pantallaAlt - 3);
      let velocitat = Math.floor(Math.random() * 10) + 1;
      vectorAsteroides.push(new Asteroide(velocitat, {x: posX, y: posY}, 3, 3));
      pantalla.append(vectorAsteroides[i].elementHTML);
    }

    // ---- Info Partida ----
    const elementNom = document.createElement("p");
    const elementPunts = document.createElement("p");
    const elementDerribats = document.createElement("p");
    const elementVides = document.createElement("p");

    elementNom.innerHTML = `Jugador: ${jugador.nom}`;
    infoPartida.append(elementNom);
    elementPunts.innerHTML = `Punts: ${jugador.punts}`;
    infoPartida.append(elementPunts);
    elementDerribats.innerHTML = `Kills: ${jugador.derribats}`;
    infoPartida.append(elementDerribats);
    elementVides.innerHTML = `Vides: ${jugador.vides}`;
    infoPartida.append(elementVides);

    // ---- Controls ----
    window.addEventListener("keydown", (event) => {
      switch(event.code) {
        case "ArrowUp": jugador.y -= jugador.velocitat; break;
        case "ArrowDown": jugador.y += jugador.velocitat; break;
      }
    });

    // ---- Comprovació de Col·lisions ----
    function comprovarCollisions() {
      vectorEnemics.forEach(enemic => {
        if (jugador.x <= enemic.x + enemic.ample &&
            jugador.x + jugador.ample >= enemic.x &&
            jugador.y <= enemic.y + enemic.alt &&
            jugador.y + jugador.alt >= enemic.y) {
          enemic.x = pantallaAmple + enemic.ample;
          jugador.punts += (nivell * 10);
          jugador.derribats++;
          infoPartida.querySelector("p:nth-child(2)").innerHTML = `Punts: ${jugador.punts}`;
          infoPartida.querySelector("p:nth-child(3)").innerHTML = `Kills: ${jugador.derribats}`;
          if (jugador.punts >= maxPunts) {
            jugador.velocitat = 0;
            vectorEnemics.forEach(e => e.velocitat = 0);
            alert("Nivell superat! :)");
          }
        }
      });
    }

    // ---- Bucle d'animació ----
    setInterval(() => {
      comprovarCollisions();
      infoPartida.querySelector("p:nth-child(4)").innerHTML = `Vides: ${jugador.vides}`;
      if (jugador.vides < 0) {
        jugador.velocitat = 0;
        setTimeout(() => location.reload(), 5000);
      }
      jugador.dibuixar(); jugador.moure();
      vectorEnemics.forEach(e => { e.dibuixar(); e.moure(); });
      vectorAsteroides.forEach(a => { a.dibuixar(); a.moure(); });
    }, fotogrames);
  })
  .catch(err => console.error("Error de la API:", err));
