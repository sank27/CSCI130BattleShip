function buildPlayerGrid(){
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

function displayAvailableShips() {
    $('#available-ships').html('');
    ships.forEach(ship => {
        createAvailableSingleShip(ship);
    });
}

function createAvailableSingleShip(ship){
    const shipDiv = `<div class='list-group-item list-group-item-action available-ship ${ship.id}' data-ship="${ship.id}">${ship.name}</div>`
    $('#available-ships').append(shipDiv);
}

function selectShip(e){
    const clickedShip = e.target;
    const shipId = $(clickedShip).data('ship');
    selectedShip = ships.find(x =>x.id == shipId);
    $('#selected-ship').html(selectedShip.name);
    $('.direction-choice').fadeTo('slow',1);
    $('#hideDirection').remove();
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

function resetForm() {
    resetDirection();
    displayAvailableShips();
    displayPlacedShips();
    finishedPlacingShip();
}

function resetDirection(){
    $('#selected-ship').html('');
    $('.direction-choice').fadeTo('slow',.6);
    $('.direction-choice').append('<div id="hideDirection" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:2;opacity:0.4;filter: alpha(opacity = 50)"></div>');
}

function showMessage(message){
    $('#message').html(message);
    $('#message').addClass('alert alert-danger');
    $('#shipModal').modal('show')
}

function removeShipFromGrid(){
    ships = staticShips;
    placedShips = [];
    buildPlayerGrid();
    displayAvailableShips();
    displayPlacedShips();
    resetForm();
}

function initializePlayerBoard() {
    try {
        buildPlayerGrid();
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
        $(document).on('click', '.removeShips', function (e) {
            removeShipFromGrid(e)
        });
    }catch(e){
        console.log(e);
    }
}

$(document).ready(function() {
    //wait for the gameid to be populated
    let existCondition = setInterval(function() {
        if (currentGame && currentGame > 0) {
            console.log("Game Exists!");
            clearInterval(existCondition);
            initializePlayerBoard();
        }
    }, 100); // check every 100ms
});