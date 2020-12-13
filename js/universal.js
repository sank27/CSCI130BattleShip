const gridCount = 10;
const routerEndPoint = "router/router.php?request=";
const responseCheck = 5; //this is in seconds
const awaitTurnCheck = 5; //this is in seconds

let canPlaceShips = false;
let selectedShip = '';
let usedBigAttack = false;

//get the current user
const userJSON = localStorage.getItem('battleuser');
const currentPlayer = userJSON ? JSON.parse(userJSON) : {};

//check if there is a new game if not create a new game
let currentGame = '';
let currentTurn = 0;
let gameStarted = false;
let currentOpponent = 0;

//game check bits
const gameCheckEndTime = 300; //seconds - 5min
//our intervals
let gameStartInterval = null;
let awaitTurnInterval = null;

//
let gameCheckStart = null;
let gameCheckEnd = null;
let myTurn = false;

const DIRECTIONS = {
    HORIZONTAL: 'horizontal',
    VERTICAL: 'vertical'
};

const ATTACKS = {
    NORMAL: 'normal',
    BIG: 'big'
};

const MESSAGETYPE = {
    NEUTRAL: 'neutral',
    POSITIVE: 'positive',
    NEGATIVE: 'negative'
};

const staticShips = [
    {order: 1, id: "carrier", name: "Carrier", abbr: "C", size: 5, row: 0, column: 0, direction: '', slots: [], hits: [], sunk: false},
    {order: 2, id: "battleship", name: "Battleship", abbr: "B", size: 4, row: 0, column: 0, direction: '', slots: [], hits: [], sunk: false},
    {order: 3, id: "destroyer", name: "Destroyer", abbr: "D", size: 3, row: 0, column: 0, direction: '', slots: [], hits: [], sunk: false},
    {order: 4, id: "submarine", name: "Submarine", abbr: "S", size: 3, row: 0, column: 0, direction: '', slots: [], hits: [], sunk: false},
    {order: 5, id: "patrolboat", name: "Patrol Boat", abbr: "P", size: 2, row: 0, column: 0, direction: '', slots: [], hits: [], sunk: false},
]

let ships = [];
let placedShips = [];
let destroyedShips = [];

const staticAttacks = [
    {order: 1, id: "torpedo", name: "Torpedo", type: ATTACKS.NORMAL},
    {order: 2, id: "big_torpedo", name: "Big Torpedo", type: ATTACKS.BIG},
]

function showMessage(message, messageType) {
    //remove class from body
    $('#message').removeClass();
    $('#message').html(message);
    switch (messageType) {
        case MESSAGETYPE.NEGATIVE:
            $('#message').addClass('alert alert-danger');
            break;
        case MESSAGETYPE.POSITIVE:
            $('#message').addClass('alert alert-success');
            break;
        default:
            $('#message').addClass('alert alert-primary');
            break;
    }
    $('#shipModal').modal('show');
}

function hideMessage(){
    $('#message').removeClass();
    $('#message').html('');
    $('#shipModal').modal('hide');
}

function gameCheck() {
    //make a call every 5 seconds to see if the opponent responds.
    //get the date
    const rightNow = new Date();
    //after 3 minutes assume they cancelled
    if (rightNow >= gameCheckEnd) {
        clearInterval(gameStartInterval);
        showMessage("No response from other player, ending game.");
        return;
    }

    $.post(`${routerEndPoint}checkgame`, {gameId: currentGame}, (data) => {
        if (data.status == 200) {
            if (data.data.started) {
                //the game has started
                //clear the interval
                clearInterval(gameStartInterval);

                //get the player who's turn it is
                gameStarted = true;
                currentTurn = data.data.turn;
                //if it's my turn set enemy board
                if (currentPlayer.userId == currentTurn) {
                    myTurn = true;
                    enableEnemyBoard();
                    //if it's my turn
                    setPlayerTurn();
                } else {
                    hideEnemyBoard();
                    startAwaitTurn();
                }

                displayGameStartText();

                hideMessage();
            }
        } else {
            showMessage(data.message);
        }
    }, 'json');
}

function displayGameStartText(){
    $('#game-started-text').html('Started');
}

function startAwaitTurn(){
    awaitTurnInterval = setInterval(awaitTurn, awaitTurnCheck * 1000);
}

function getGameLog(){
    $.post(`${routerEndPoint}getmessages`, {gameId: currentGame}, (data) => {
        if (data.status == 200) {
            const messages = data.data;
            $('#attack_log').html(''); //clear the log
            //display the messages
            if (messages && messages.length > 0){
                messages.forEach(x => {
                    $('#attack_log').append(`<br>${x}`);
                });
            }

        } else {
            showMessage(data.message);
        }
    },'json');
}

function awaitTurn() {
    //check the game to see if it's my turn,
    $.post(`${routerEndPoint}getturn`, {gameId: currentGame}, (data) => {
        if (data.status == 200) {
            currentTurn = data.data.turn;
            const finished = data.data.finished;
            const winner = data.data.winner;

            if (finished){
                let winningMessage = '';
                clearInterval(awaitTurnInterval);
                if (winner == 0){
                    winningMessage = 'The Game has ended, no winner';
                }else{
                    if (winner == currentPlayer.userId){
                        winningMessage = 'The Game has ended, you are the winner';
                    }else{
                        winningMessage = 'The Game has ended, your opponent won';
                    }
                }

                $('#game-over-message').html(winningMessage);
                $('#gameOver').modal('show');
            }else {
                //when it's my turn, turn on the enemy board and allow 1 attack
                if (currentPlayer.userId == currentTurn) {
                    clearInterval(awaitTurnInterval);

                    //update my board from attack
                    updateShips();
                    myTurn = true;
                    enableEnemyBoard();
                    setPlayerTurn();
                } else {
                    hideEnemyBoard();
                    otherPlayerTurn();
                }
                getGameLog();
            }
        } else {
            showMessage(data.message);
        }
    }, 'json');
}