//get the available attacks from the backend
//update the big attack variable

//create the enemy board
function buildEnemyGrid() {
    $('#enemy-table').html('');
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

    $('#enemy-table').html(gridString);
}

function displayAvailableAttacks() {
    $('#available-attacks').html('');
    //build the attacks

    ships.forEach(attack => {
        if (attack != ATTACKS.BIG){
            createAvailableAttack(attack);
        }else{
            if (!usedBigAttack){
                createAvailableAttack(attack);
            }
        }
    });
}

function createAvailableAttack(attack)
{
    attackDiv = `<div class='list-group-item list-group-item-action available-ship ${ship.id}' data-ship="${ship.id}">${ship.name}</div>`
    $('#available-ships').append(shipDiv);
}



//put the attacks on the board
staticAttacks

//clicks send attacks to the backend
//click mark a square red

function initializeEnemyBoard(){
    try {
        buildEnemyGrid();
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
            initializeEnemyBoard();
        }
    }, 100); // check every 100ms
});