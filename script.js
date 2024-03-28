let currentNumber;
let timer;
var timeLimit = 4000; // initial time limit in milliseconds
let score = 0;
let gameOver = false;
let gameStarted = false;

function startGame() {
    score = 0;
    timeLimit = 4000; // Reset initial time limit
    displayNewNumber();
    document.addEventListener('keydown', handleKeyDown);
    document.getElementById('startButton').style.display = 'none';
    document.getElementById('restartButton').style.display = 'none';
    document.getElementById('message').textContent = '';
    document.getElementById('timer').style.display = 'block';
    updateTimer();
    gameStarted = true;
}

function displayNewNumber() {
    currentNumber = Math.floor(Math.random() * 3) + 1; // Generate random number between 1 and 3
    document.getElementById('number').textContent = currentNumber;
    document.getElementById('number').classList.remove('correct'); // Remove 'correct' class if it exists
    clearInterval(timer); // Clear previous timer
    timer = setInterval(() => {
        timeLimit -= 100 // Decrease time limit
        updateTimer();
        if (timeLimit <= 0) {
            clearInterval(timer);
            gameOverHandler('tooSlow');
        }
    }, 100);
}

function handleKeyDown(event) {
    if (!gameStarted) return; // Do nothing if the game hasn't started yet
    const pressedKey = parseInt(event.key);
    if (!isNaN(pressedKey) && pressedKey === currentNumber) {
        clearInterval(timer); // Clear previous timer
        score++;
        updateScore();
        document.getElementById('number').classList.add('correct'); // Add 'correct' class for animation
        setTimeout(() => {
            document.getElementById('number').classList.remove('correct'); // Remove 'correct' class after animation
            if (timeLimit >= 1200) {
                timeLimit -= 50; // Reset time limit
            } else {
                timeLimit = 1200;
            }
            displayNewNumber(); // Display new number
        }, 200);
    } else {
        gameOverHandler('wrongNumber');
    }
}

function gameOverHandler(reason) {
    gameOver = true;
    clearInterval(timer); // Clear timer
    document.removeEventListener('keydown', handleKeyDown);
    if (reason === 'tooSlow') {

        document.getElementById('message').textContent = 'Too slow! Your final score: ' + score;
    } else if (reason === 'wrongNumber') {
        document.getElementById('message').textContent = 'Wrong number! Your final score: ' + score;
    }
    updateHighscore()
    document.getElementById('restartButton').style.display = 'inline';
}
function updateHighscore() {
    const score = document.getElementById('score').textContent.replace('Score: ', '');
    // Send score to game.php for updating highscore
    fetch('game.php', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'score=' + score
    });
};


function updateScore() {
    document.getElementById('score').textContent = 'Score: ' + score;
}

function updateTimer() {
    let timeLeft = timeLimit / 1000;
    document.getElementById('timer').textContent = 'Time left: ' + timeLeft.toFixed(1);
}

document.getElementById('startButton').addEventListener('click', () => {
    startGame(); // Start game
});

document.getElementById('restartButton').addEventListener('click', () => {
    gameOver = false;
    startGame(); // Restart game
});

document.getElementById('numberButton1').addEventListener('click', () => {
    handleButtonClick(1);
});

document.getElementById('numberButton2').addEventListener('click', () => {
    handleButtonClick(2);
});

document.getElementById('numberButton3').addEventListener('click', () => {
    handleButtonClick(3);
});

function handleButtonClick(pressedKey) {
    if (!gameStarted) return; // Do nothing if the game hasn't started yet
    if (pressedKey === currentNumber) {
        clearInterval(timer); // Clear previous timer
        score++;
        updateScore();
        document.getElementById('number').classList.add('correct'); // Add 'correct' class for animation
        setTimeout(() => {
            document.getElementById('number').classList.remove('correct'); // Remove 'correct' class after animation
            if (timeLimit >= 1800) {
                timeLimit -= 100; // Reset time limit
            } else {
                timeLimit = 1800;
            }
            displayNewNumber(); // Display new number
        }, 200);
    } else {
        gameOverHandler('wrongNumber');
    }
}