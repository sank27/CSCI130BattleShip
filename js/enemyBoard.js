//get the available attacks from the backend
//update the big attack variable

let selectedAttack = '';
let attackEnabled = false;

//create the enemy board
function buildEnemyGrid() {
    $('#enemy-table').html('');
    let grid = [];
    for (let x = 1; x <=gridCount; x++){
        let row = "<tr>";
        for(let y = 1; y<=gridCount; y++){
            row += `<td class='cell attackCell' id="${x}-${y}" data-row="${x}" data-column="${y}"></td>`;
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

    staticAttacks.forEach(attack => {
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
    attackDiv = `<div class='list-group-item attacks list-group-item-action ${attack.id}' data-type="${attack.type}" data-ship="${attack.id}">${attack.name}</div>`
    $('#available-attacks').append(attackDiv);
}

function setSelectedAttack(e){
    if (!e || !e.target){
        return;
    }

    //remove highlighting
    $('.attacks').removeClass("highlighted-attack");
    //highlight this attack


    const attack = e.target;
    selectedAttack = $(attack).data('type');
    $(attack).addClass('highlighted-attack');
    //make attack cells pointers
    $('.attackCell').addClass('clickable');
    attackEnabled = true;
}

//if an attack is selected, make the square red, only allow one
function handleAttackClick(e){
    if (!e || !e.target){
        return;
    }
    if (!attackEnabled){
        return;
    }
    const cell = e.target;
    //color the cell
    $(cell).addClass('did-attack');
    $(cell).removeClass('attackCell');
    attackEnabled = false;

    //send the attack to the backend and wait for response
    console.log(currentGame);
}

function hideBoard(){
    $('#enemy-board').fadeTo('slow',.6);
    $('#enemy-board').append('<div id="hide-enemy-board" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:2;opacity:0.4;filter: alpha(opacity = 50)"></div>');
}

//after an attack is sent to the backend, check every 1/2 second for a response
//it will switch turns after a response

function initializeEnemyBoard(){
    try {
        buildEnemyGrid();
        displayAvailableAttacks();
    }catch(e){
        console.log(e);
    }
}

$(document).on("click",".attacks",function(e) {
    setSelectedAttack(e);
});

$(document).on("click",".attackCell", function(e){
    handleAttackClick(e);
});

$(document).ready(function() {
    //wait for the gameid to be populated
    let existCondition = setInterval(function() {
        if (currentGame && currentGame > 0) {
            console.log("Game Exists!");
            clearInterval(existCondition);
            initializeEnemyBoard();
        }
    }, 100); // check every 100ms

    hideBoard();
});