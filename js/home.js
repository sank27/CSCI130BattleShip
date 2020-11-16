const responseCheck = 5; //this is in seconds
const lastCheck = 180; //this is in seconds
const successfulResponse = false;

let currentRequest = 0
let requestedOpponent = 0;
let opponents = [];
let checkStartDate = null;
let checkEndTime = null;
let checkInterval = null;

let incomingRequest = 0;
let incomingInterval = null;

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
        const newOpponent = `<div class="card singleOpponent" id="${x.id}">
                            <img src="assets/user.png" class="card-img-top user-image" alt="User Image">
                            <div class="card-body">
                                <h5 class="card-title">${x.player}</h5>
                                <button id="${x.id}" class="btn btn-primary request">Request</button>
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
    $.post(`${routerEndPoint}checkmyrequests`, (data) => {
        if(data.status == 200) {
            if (data.data && data.data.id){
                //get the request id and user
                incomingRequest = data.data.request;
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

function showIncomingRequest($requestor){
    //show the incoming request with a yes or no button
    alert('incoming request display');
}

function declineIncomingRequest(){
    //decline the request

    //restart the interval
    incomingInterval = setInterval(checkIncomingRequests, responseCheck * 1000);
}

function approveIncomingRequest(){
    //make sure the request is still valid

    //cancel any send requests

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

checkForExistingGames();





