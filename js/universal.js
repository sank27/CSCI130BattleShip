const gridCount = 10;
const routerEndPoint = "router/router.php?request=";
const responseCheck = 5; //this is in seconds

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

//
let gameCheckStart = null;
let gameCheckEnd =  null;

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
    {order: 1, id: "carrier", name: "Carrier", abbr:"C", size: 5, row: 0, column: 0, direction: '', slots: []},
    {order: 2, id: "battleship", name: "Battleship", abbr:"B", size: 4, row: 0, column: 0, direction: '', slots: []},
    {order: 3, id: "destroyer", name: "Destroyer", abbr:"D", size: 3, row: 0, column: 0, direction: '', slots: []},
    {order: 4, id: "submarine", name: "Submarine", abbr:"S", size: 3, row: 0, column: 0, direction: '', slots: []},
    {order: 5, id: "patrolboat", name: "Patrol Boat", abbr:"P", size: 2, row: 0, column: 0, direction: '', slots: []},
]

let ships = [];
let placedShips = [];
let destroyedShips = [];

const staticAttacks = [
    {order: 1, id:"torpedo", name: "Torpedo", type: ATTACKS.NORMAL},
    {order: 2, id:"big_torpedo", name: "Big Torpedo", type: ATTACKS.BIG},
]

function showMessage(message, messageType){
    //remove class from body
    $('#message').removeClass();
    $('#message').html(message);
    switch(messageType){
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
}

function gameCheck(){
    //make a call every 5 seconds to see if the opponent responds.
    //get the date
    const rightNow = new Date();
    //after 3 minutes assume they cancelled
    if (rightNow >= gameCheckEnd){
        clearInterval(gameStartInterval);
        showMessage("No response from other player, ending game.");
        return;
    }

    $.post(`${routerEndPoint}checkgame`, {gameid: currentGame}, (data) => {
        if(data.status == 200) {
            //if the game has started
            //clearInterval(gameStartInterval);
            //check turn
            //else keep waiting
        }else{
            showMessage(data.message);
        }
    },'json');
}

function awaitTurn(){
    //check the game to see if it's my turn,
    //when it's my turn, turn on the enemy board and allow 1 attack
    //once finished with attack...turn on await my turn
}