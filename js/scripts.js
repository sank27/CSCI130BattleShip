const gridCount = 10;
let canPlaceShips = false;
let selectedShip = '';

//#game-start - hide the game start area

let ships = [
	{id: "carrier", name: "Carrier", abbr:"C", size: 5, row: 0, column: 0, direction: ''},
	{id: "battleship", name: "Battleship", abbr:"B", size: 4, row: 0, column: 0, direction: ''},
	{id: "destroyer", name: "Destroyer", abbr:"D", size: 3, row: 0, column: 0, direction: ''},
	{id: "submarine", name: "Submarine", abbr:"S", size: 3, row: 0, column: 0, direction: ''},
	{id: "patrolboat", name: "Patrol Boat", abbr:"P", size: 2, row: 0, column: 0, direction: ''},
]

let placedShips = [

];

const destroyedShips = [

];

function buildGrid(){
	$('#player-table').html('');
	let grid = [];
	for (let x = 1; x <=gridCount; x++){
		let row = "<tr>";
		for(let y = 1; y<=gridCount; y++){
			row += `<td class='cell' id="${x}-${y}" data-row="${x}" data-column="${y}"></td>`;
		}
		row += "</tr>";
		grid.push(row);
	}

	const gridString = grid.join(' ');

	$('#player-table').html(gridString);
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
	selectedShip.row = $(selectedCell).data('row');
	selectedShip.column = $(selectedCell).data('column');
	selectedShip.direction = $("input[name='shipDirection']:checked").val();
	placedShips.push(selectedShip);
	ships = ships.filter(x => x.id != selectedShip.id);

	selectedShip = '';
	canPlaceShips = false;
	resetForm();
	displayAvailableShips();
	displayPlacedShips();
	finishedPlacingShip();
}

function removeShipFromGrid(){

}

function displayPlacedShips() {
	placedShips.forEach(ship => {
		if (ship.direction == 'horizontal'){
			placeHorizontal(ship);
		}
		if (ship.direction == 'vertical'){
			placeVertical(ship);
		}
	});
}

function placeVertical(ship){
	//starting point
	for(let x = ship.row; x < ship.row + ship.size; x++){
		const position = `#${x}-${ship.column}`;
		$(position).css("background-color","gray");
		$(position).html(ship.abbr);
	}
}

function placeHorizontal(ship){
	for(let x = ship.column; x < ship.column + ship.size; x++){
		const position = `#${ship.row}-${x}`;
		$(position).css("background-color","gray");
		$(position).html(ship.abbr);
	}
}

function resetForm(){
	$('#selected-ship').html('');
	$('.direction-choice').hide();
}

$(document).ready(function() {
	buildGrid();
	displayAvailableShips();
	displayPlacedShips();
	resetForm();

	$(document).on('click', '.available-ship',function(e){ selectShip(e) });
	$(document).on('click', '.cell', function(e){ addShipToGrid(e)});
});
