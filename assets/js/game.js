// OBTENEMOS LA LONGITUD DINÁMICA DEL HTML
const configDiv = document.getElementById('game-config');
const WORD_LENGTH = configDiv ? parseInt(configDiv.getAttribute('data-length')) : 5;

const MAX_ATTEMPTS = 6;
let currentRow = 0;
let currentTile = 0;
let guess = "";
const rows = document.querySelectorAll('.row');

// LISTENERS
window.addEventListener('keydown', (e) => {
    if (document.getElementById('modal-educativo').style.display === 'flex') return;
    
    const key = e.key;
    if (key === 'Enter') submitGuess();
    else if (key === 'Backspace') deleteLetter();
    else if (/^[a-zA-ZñÑ]$/.test(key)) addLetter(key.toUpperCase());
});

const keys = document.querySelectorAll('.key');
keys.forEach(k => {
    k.addEventListener('click', () => {
        const val = k.getAttribute('data-key');
        if (val === 'Enter') submitGuess();
        else if (val === 'Backspace') deleteLetter();
        else addLetter(val);
    });
});

const btnPista = document.getElementById('btn-pista');
if (btnPista) {
    btnPista.addEventListener('click', () => {
        document.getElementById('pista-texto').style.display = "block";
        btnPista.style.display = "none";
    });
}

const btnCerrar = document.getElementById('btn-cerrar-modal');
if (btnCerrar) {
    btnCerrar.addEventListener('click', () => {
        document.getElementById('modal-educativo').style.display = "none";
    });
}

// LÓGICA
function addLetter(letter) {
    // Usamos la variable WORD_LENGTH dinámica
    if (currentTile < WORD_LENGTH && currentRow < MAX_ATTEMPTS) {
        const row = rows[currentRow];
        const tile = row.querySelectorAll('.cell')[currentTile];
        tile.textContent = letter;
        tile.classList.add('active');
        tile.style.borderColor = "#f02109";
        guess += letter;
        currentTile++;
    }
}

function deleteLetter() {
    if (currentTile > 0) {
        currentTile--;
        const row = rows[currentRow];
        const tile = row.querySelectorAll('.cell')[currentTile];
        tile.textContent = "";
        tile.classList.remove('active');
        tile.style.borderColor = "#3a3a3c";
        guess = guess.slice(0, -1);
    }
}

async function submitGuess() {
    if (guess.length !== WORD_LENGTH) return; // Esperamos a la longitud correcta

    const response = await fetch('check_word.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            guess: guess,
            isLastAttempt: (currentRow === MAX_ATTEMPTS - 1)
        })
    });

    const data = await response.json();

    if (data.status === "success") {
        revealColors(data.result);
        
        if (data.win && !data.guest && data.newPoints !== null) {
            const pElem = document.getElementById('user-points');
            const lElem = document.getElementById('user-level');
            if (pElem) pElem.textContent = data.newPoints;
            if (lElem) lElem.textContent = data.newLevel;
        }

        if (data.gameOver) {
            setTimeout(() => mostrarFicha(data.educativo, data.win), 1500);
        } else {
            currentRow++;
            currentTile = 0;
            guess = "";
        }
    }
}

function revealColors(result) {
    const row = rows[currentRow];
    const tiles = row.querySelectorAll('.cell');

    result.forEach((status, index) => {
        const tile = tiles[index];
        tile.classList.add(status);
        tile.style.borderColor = "transparent";
        
        const letra = tile.textContent;
        const tecla = document.querySelector(`.key[data-key='${letra}']`);
        if (tecla) {
            if (status === 'correct') tecla.style.backgroundColor = '#f02109';
            else if (status === 'present' && tecla.style.backgroundColor !== 'rgb(240, 33, 9)') 
                tecla.style.backgroundColor = '#c9b458';
            else if (status === 'absent' && tecla.style.backgroundColor !== 'rgb(240, 33, 9)' && tecla.style.backgroundColor !== 'rgb(201, 180, 88)') {
                tecla.style.backgroundColor = '#3a3a3c';
            }
        }
    });
}

function mostrarFicha(info, gano) {
    const modal = document.getElementById('modal-educativo');
    if (modal && info) {
        document.getElementById('modal-titulo').textContent = gano ? "¡ACERTASTE!" : "LA PALABRA ERA:";
        document.getElementById('modal-descripcion').textContent = info.descripcion;
        document.getElementById('modal-imagen').src = info.imagen_url;
        modal.style.display = "flex";
    }
}