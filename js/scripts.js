const gridCount = 10;
let canPlaceShips = false;
let canDeleteShips = false;
let selectedShip = '';

const DIRECTIONS = {
	HORIZONTAL: 'horizontal',
	VERTICAL: 'vertical'
}

//#game-start - hide the game start area

//get the ships from the backend on refresh

const staticShips = [
	{order: 1, id: "carrier", name: "Carrier", abbr:"C", size: 5, row: 0, column: 0, direction: '', slots: []},
	{order: 2, id: "battleship", name: "Battleship", abbr:"B", size: 4, row: 0, column: 0, direction: '', slots: []},
	{order: 3, id: "destroyer", name: "Destroyer", abbr:"D", size: 3, row: 0, column: 0, direction: '', slots: []},
	{order: 4, id: "submarine", name: "Submarine", abbr:"S", size: 3, row: 0, column: 0, direction: '', slots: []},
	{order: 5, id: "patrolboat", name: "Patrol Boat", abbr:"P", size: 2, row: 0, column: 0, direction: '', slots: []},
]

let ships = [];
const placedShips = [];
const destroyedShips = [];

$(document).ready(function() {
	try {
		buildGrid();
		getShips();
		displayAvailableShips();
		displayPlacedShips();
		resetForm();

		$(document).on('click', '.available-ship', function (e) {
			selectShip(e)
		});
		$(document).on('click', '.addCell', function (e) {
			addShipToGrid(e)
		});
		$(document).on('click', '.removeCell', function (e) {
			removeShipFromGrid(e)
		});
	}catch(e){
		console.log(e);
	}
});

function buildGrid(){
	$('#player-table').html('');
	let grid = [];
	for (let x = 1; x <=gridCount; x++){
		let row = "<tr>";
		for(let y = 1; y<=gridCount; y++){
			row += `<td class='cell addCell' id="${x}-${y}" data-row="${x}" data-column="${y}"></td>`;
		}
		row += "</tr>";
		grid.push(row);
	}

	const gridString = grid.join(' ');

	$('#player-table').html(gridString);
}

function getShips() {
	//get the ships from the backend

	//if there are no ships put in all the ships
	ships = staticShips;
}

//

function displayAvailableShips() {
	$('#available-ships').html('');
	ships.forEach(ship => {
		createAvailableSingleShip(ship);
	});
}

function createAvailableSingleShip(ship){
	shipDiv = `<div class='list-group-item list-group-item-action available-ship ${ship.id}' data-ship="${ship.id}">${ship.name}</div>`
	$('#available-ships').append(shipDiv);
}

function selectShip(e){
	const clickedShip = e.target;
	const shipId = $(clickedShip).data('ship');
	selectedShip = ships.find(x =>x.id == shipId);
	$('#selected-ship').html(selectedShip.name);
	$('.direction-choice').show();
	canPlaceShips = true;
	startplacingShip();
}

function startplacingShip(){
	$('#player-table').css('cursor', 'pointer');
}

function finishedPlacingShip(){
	$('#player-table').css('cursor', 'default');
}

function addShipToGrid(e){
	if (!canPlaceShips){
		return;
	}
	const selectedCell = e.target;
	row = $(selectedCell).data('row');
	column = $(selectedCell).data('column');
	direction = $("input[name='shipDirection']:checked").val();

	if (direction === DIRECTIONS.VERTICAL) {
		const successfulVerticalCheck = directionCheck(row);
		if (!successfulVerticalCheck) {
			//Display some error
			showMessage('You cannot place a ship there (Off the bottom of the grid)');
			return;
		}
	}

	if (direction === DIRECTIONS.HORIZONTAL) {
		const successfulHorizontalCheck = directionCheck(column)
		if (!successfulHorizontalCheck) {
			showMessage('You cannot place a ship there (Off the edge of the grid)');
			return;
		}
	}

	//ship check
	const successfulShipCheck = shipCheck(row, column, direction);
	if (!successfulShipCheck){
		showMessage('You cannot place a ship on top of another ship');
		return;
	}

	//save the choices to the selected ship
	selectedShip.row = row;
	selectedShip.column = column;
	selectedShip.direction = direction;

	//add all the slots...makes it easier to catch overlap later
	selectedShip.slots = getShipSlots(row,column, direction);
	placedShips.push(selectedShip);
	ships = ships.filter(x => x.id != selectedShip.id);

	selectedShip = '';
	canPlaceShips = false;
	resetForm();
}

function getShipSlots(row, column, direction) {
	let slots = [];
	if (direction === DIRECTIONS.VERTICAL){
		for(let i=row; i<row+selectedShip.size; i++){
			slots.push(`${i}|${column}`);
		}
	}
	if (direction === DIRECTIONS.HORIZONTAL){
		for(let i=column; i<column+selectedShip.size; i++){
			slots.push(`${row}|${i}`);
		}
	}
	return slots;
}


function directionCheck(iterator){
	//make sure they don't go off the screen
	const shipSize = selectedShip.size;
	for(let x = iterator; x <iterator + shipSize; x++){
		if (x > gridCount){
			return false;
		}
	}

	return true;
}

function shipCheck(row, column, direction){
	//get the ship from placedShips
	//get all the slots
	const shipSlots = getShipSlots(row, column, direction);
	//have to use a for loop
	for(let x=0; x<placedShips.length; x++){
		const currentShipSlots = placedShips[x].slots;
		if(currentShipSlots.some(shipPosition => shipSlots.includes(shipPosition))){
			return false;
		}
	}

	return true;
}

function removeShipFromGrid(){
	const foundShip = {};
	//reset the pieces
	foundShip.row = 0;
	foundShip.column = 0
	selectedShip.direction = '';
	selectedShip.slots = [];
	ships.push(foundShip);
	//sort
	ships.sort((a,b) => (a.order < b.order) ? 1 : -1);
	resetForm();
}

function displayPlacedShips() {
	placedShips.forEach(ship => {
		const slots = ship.slots;
		slots.forEach(singlePosition =>{
			const positions = singlePosition.split('|');
			const row = positions[0];
			const column = positions[1];
			const position = `#${row}-${column}`;
			placeCell(position, ship);
		});
	});
}

function placeCell(cell, ship){
	$(cell).css("background-color","gray");
	$(cell).html(ship.abbr);
	$(cell).attr('data-ship', ship.id);
	$(cell).removeClass('addCell');
	$(cell).addClass('removeCell');
}

function clearCell(cell){
	$(cell).css("background-color","#fff");
	$(cell).html('');
	$(cell).removeAttr('data-ship');
	$(cell).addClass('addCell');
	$(cell).removeClass('removeCell');
}

function resetForm() {
	resetDirection();
	displayAvailableShips();
	displayPlacedShips();
	finishedPlacingShip();
}

function resetDirection(){
	$('#selected-ship').html('');
	$('.direction-choice').hide();
}

function showMessage(message){
	$('#message').html(message);
	$('#message').addClass('alert alert-danger');
	$('#shipModal').modal('show')
}

function resetShips(){

}


