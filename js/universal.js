const gridCount = 10;
const routerEndPoint = "router/router.php?request=";
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

const DIRECTIONS = {
    HORIZONTAL: 'horizontal',
    VERTICAL: 'vertical'
}

const ATTACKS = {
    NORMAL: 'normal',
    BIG: 'big'
}

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