//get the current game
function checkForExistingGames() {
//see if there is an existing started game
    $.get(`${routerEndPoint}getgame`, (data) => {
        //if there is a started game, redirect to the battleship page
        if (data.data && data.data.id) {
            //set the game data....
            currentGame = data.data.id;
            currentTurn = data.data.turn;
            //get the opponent info

        } else {
            //no current game? redirect to home
            window.location.href = "home.php";
        }
    },'json');
}

checkForExistingGames();