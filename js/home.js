const lastCheck = 300; //this is in seconds
const successfulResponse = false;

const playerCheck = 5; //this is in seconds

let currentRequest = 0
let requestedOpponent = 0;
let opponents = [];
let checkStartDate = null;
let checkEndTime = null;
let checkInterval = null;

let incomingRequest = 0;
let incomingInterval = null;

let playerInterval = null;

function checkForExistingRequests(){
    //this checks to see if there are any existing requests
    $.get(`${routerEndPoint}checkexistingrequests`,(data)=> {
        if (data.status == 200) {
            if (data.data.id != false) {
                currentRequest = data.data.id;
                requestedOpponent = data.data.requestee;
                $('.request').prop('disabled', true);
                //start checking the responses
                //get the date from the backend and add some many seconds
                const dateTimeParts = data.data.created.split(/[- :]/);
                dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
                checkStartDate = new Date(...dateTimeParts);
                checkEndTime = new Date(...dateTimeParts);
                checkEndTime.setSeconds(checkStartDate.getSeconds() + lastCheck);
                checkInterval = setInterval(checkResponse, responseCheck * 1000);
            }
        }else{
            showMessage(data.message);
        }
    },'json')
}

//get the opponents
function getAvailableOpponents(){
    $.get(`${routerEndPoint}getplayers`,(data)=> {
        opponents = data.data;
        displayOpponents();
    },'json')
}

//display the opponents
function displayOpponents() {
    $('#opponents').html('');
    opponents.forEach(x => {
        const isDisabled = requestedOpponent ? 'disabled' : '';
        const newOpponent = `<div class="card singleOpponent" id="${x.id}">
                            <img src="assets/user.png" class="card-img-top user-image" alt="User Image">
                            <div class="card-body">
                                <h5 class="card-title">${x.player}</h5>
                                <button id="${x.id}" class="btn btn-primary request" ${isDisabled}>Request</button>
                            </div>
                        </div>`;
        $('#opponents').append(newOpponent);
    });
}

function makeRequest(id){
    $.post(`${routerEndPoint}makerequest`, {opponent: id}, (data) => {
        if (data.status == 200) {
            requestedOpponent = id;
            //disable all the request buttons until the request finished
            currentRequest = data.data;
            $('.request').prop('disabled', true);
            //start checking the responses
            checkStartDate = new Date();
            checkEndTime =  new Date();
            checkEndTime.setSeconds(checkStartDate.getSeconds() + lastCheck);
            checkInterval = setInterval(checkResponse, responseCheck * 1000);
        }else{
            showMessage(data.message);
        }
    },'json');
}

function resetMessage(){
    $('message').removeClass();
    $('message').html('');
}

function showMessage(message){
    resetMessage();
    $('#message').html(message);
    $('#message').addClass('alert alert-danger');
    $('#messageModal').modal('show')
}

function showPositiveMessage(message){
    resetMessage();
    $('#message').html(message);
    $('#message').addClass('alert alert-success');
    $('#messageModal').modal('show')
}

function checkResponse(){
    //make a call every 5 seconds to see if the opponent responds.
    //get the date
    const rightNow = new Date();
    //after 3 minutes assume they cancelled
    if (rightNow >= checkEndTime){
        clearInterval(checkInterval);
        showMessage("No response from other player...try again.");
        $.post(`${routerEndPoint}cleanuprequest`, {requestid: currentRequest});
        requestedOpponent = 0;
        currentRequest = 0;
        $('.request').prop('disabled', false);
        return;
    }

    $.post(`${routerEndPoint}checkresponse`, {gamerequest: currentRequest}, (data) => {
        if(data.status == 200) {
            if (data.data == 'pending') {
                //do not do anything
            }else if (data.data == 'approved'){
                clearInterval(checkInterval);
                showPositiveMessage("Other player approved, taking you to the game");
                currentRequest = 0;
                //do not re-enable the buttons

                //create the game here and redirect
                createGame();
            }else {
                clearInterval(checkInterval);
                showMessage("Sorry, other player declined...try again.");
                //clean up the sql
                $.post(`${routerEndPoint}cleanuprequest`, {requestid: currentRequest});
                requestedOpponent = 0;
                currentRequest = 0;
                $('.request').prop('disabled', false);
            }
        }else{
            showMessage(data.message);
        }
    },'json');
}

function createGame(){
    $.post(`${routerEndPoint}creategame`, {opponent: requestedOpponent}, (data) => {
        if(data.status == 200) {
            //the game has been created redirect to battleship
            window.location.href = "battleship.php";
        }else{
            showMessage(data.message);
        }
    },'json');
}

function checkIncomingRequests(){
    $.get(`${routerEndPoint}checkmyrequests`, (data) => {
        if(data.status == 200) {
            if (data.data && data.data.id){
                //get the request id and user
                incomingRequest = data.data.id;
                showIncomingRequest(data.data.user);

                //pause the intervals
                clearInterval(incomingInterval);
            }else{
                console.log('no current requests');
            }
        }else{
            showMessage(data.message);
        }
    }, 'json');
}

function checkForPlayers() {
    playerInterval = setInterval(getAvailableOpponents, playerCheck * 1000);
}

function showIncomingRequest($requestor){
    //show the incoming request with a yes or no button
    $('#game-requestor').html($requestor);
    $('#approvedeclineModal').modal('show');
}

function declineIncomingRequest(){
    //decline the request
    $('#approvedeclineModal').modal('hide');
    $.post(`${routerEndPoint}declinegamerequest`, {requestid: incomingRequest}, (data) => {
        if(data.status == 200) {
            showMessage(data.message);
            requestedOpponent = 0;
            incomingRequest = 0;
            //enable the buttons
            $('.request').prop('disabled', false);
            //restart the interval
            incomingInterval = setInterval(checkIncomingRequests, responseCheck * 1000);
        }else{
            showMessage(data.message);
        }
    },'json');
}

function approveIncomingRequest(){
    $('#approvedeclineModal').modal('hide');
    $.post(`${routerEndPoint}acceptgamerequest`, {requestid: incomingRequest}, (data) => {
        if(data.status == 200) {
            setTimeout(() => {  checkForExistingGames(); }, 5000);
        }else{
            showMessage(data.message);
        }
    },'json');
}

function checkForExistingGames() {
//see if there is an existing started game
    $.get(`${routerEndPoint}getgame`, (data) => {
        //if there is a started game, redirect to the battleship page
        if (data.data.id) {
            //do redirect
            window.location.href = "battleship.php";
        } else {
            //if there is no started game, get and show opponents
            getAvailableOpponents();
            //start interval to check for requests
            incomingInterval = setInterval(checkIncomingRequests, responseCheck * 1000);
        }
    },'json');
}

$(document).on("click",".request", function(e){
    if (!e.target || !e.target.id){
        return;
    }
    makeRequest(e.target.id);
});

$(document).on("click", "#accept-game", function(){
    approveIncomingRequest();
});

$(document).on("click", "#decline-game", function(){
    declineIncomingRequest();
});

checkForExistingGames();
checkForPlayers();
checkForExistingRequests();






