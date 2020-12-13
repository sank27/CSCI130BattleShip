//get the available attacks from the backend
//update the big attack variable

let selectedAttack = '';
let attackEnabled = false;
let existingAttacks = [];

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

function getExistingAttacks(){
    if (currentGame){
        $.post(`${routerEndPoint}getattacks`, {gameId: currentGame}, (data) => {
            if (data.status == 200) {
                existingAttacks = data.data;
                displayExistingAttacks();
            }
        }, 'json');
    }
}

function displayExistingAttacks(){
    if (existingAttacks && existingAttacks.length > 0) {
        existingAttacks.forEach(singlePosition => {
            const positions = singlePosition.attack.split('-');
            const row = positions[0];
            const column = positions[1];
            const position = `#enemy-table #${row}-${column}`;
            const hit = singlePosition.hit ? true : false;
            placeAttack(position, hit);
        });
    }
}

function placeAttack(cell, hit){
    const classCell = hit ? 'success-attack' : 'did-attack';
    $(cell).addClass(classCell);
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

    //send gameid, attack coords
    const attack = $(cell).attr('id');
    $.post(`${routerEndPoint}attackopponent`, {gameId: currentGame, attack: attack}, (data) => {
        console.log(data);
        if (data.status == 200) {
            if (data.data.success){
                $(cell).removeClass('did-attack');
                $(cell).addClass('success-attack');
            }
            //restart turn system
            hideEnemyBoard();

            $('#available-attacks .attacks').each((key, value)=> {
                $(value).removeClass('highlighted-attack');
            });
            //remove torpedo class
            otherPlayerTurn();

            //disable the attack
            myTurn = false;
            startAwaitTurn();
        } else {
            showMessage(data.message);
        }
        //if the cell hit the opponent ship change the color to yellow
    },'json');
}

function setPlayerTurn(){
    $('#turn-placement').html('It is your turn, please pick an attack');
    $('#turn-placement').removeClass();
    $('#turn-placement').addClass('alert alert-success');
}

function otherPlayerTurn(){
    $('#turn-placement').html('It is your opponent\'s turn, please wait');
    $('#turn-placement').removeClass();
    $('#turn-placement').addClass('alert alert-danger');
}

function hideEnemyBoard(){
    $('#enemy-board').fadeTo('slow',.6);
    //see if it exists
    const hideBoard = $(document).find('#hide-enemy-board');
    if(hideBoard.length == 0) {
        $('#enemy-board').append('<div id="hide-enemy-board" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:2;opacity:0.4;filter: alpha(opacity = 50)"></div>');
    }
}

function enableEnemyBoard(){
    $('#enemy-board').fadeTo('slow',1);
    $('#hide-enemy-board').remove();
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
    let existEnemyCondition = setInterval(function() {
        if (currentGame && currentGame > 0) {
            console.log("Game Exists!");
            clearInterval(existEnemyCondition);
            initializeEnemyBoard();
            getExistingAttacks();
        }
    }, 100); // check every 100ms

    //wait for my turn

    hideEnemyBoard();
});