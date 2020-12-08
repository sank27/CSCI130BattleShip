<?php
include_once('components/functions.php');
$additionalCss = array('css/universal.css','css/styles.css');
echo includeHeader($additionalCss);
include_once('components/auth.php');
include_once('components/header.php');
//decided to separate the header from the other pieces
?>
<div class="container">
    <div class="row">
        <div class="col-6" id="player-board">
            <h4 class="text-center">Player Board</h4>
            <div class="row">
                <div class="col-4">
                    <div id="game-start">
                        <div id="available-ships" class="list-group">
                        </div>
                        <div class="mt-4 text-center">
                            <span class="font-weight-bold">Selected Ship:</span>
                            <div id="selected-ship" class="mb-3"></div>
                            <div class="direction-choice text-left">
                                <div class="text-center border-bottom">Ship Placement</div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipDirection" id="shipHorizontal"
                                           value="horizontal" checked>
                                    <label class="form-check-label" for="shipHorizontal">
                                        Horizontally
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipDirection" id="shipVertical"
                                           value="vertical">
                                    <label class="form-check-label" for="shipVertical">
                                        Vertically
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <button class="removeShips btn btn-danger">Remove Ship(s)</button>
                            <button class="gameStart btn btn-success mt-4 text-center">Start Game</button>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <table id="player-table"></table>
                </div>
            </div>
        </div>
        <div class="col-6" id="enemy-board">
            <h4 class="text-center">Enemy Board</h4>
            <div class="row">
                <div class="col-8">
                    <table id="enemy-table"></table>
                </div>
                <div class="col-4">
                    <div id="game-start">
                        <div id="available-attacks" class="list-group">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="shipModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">System Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="" id="message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
    $additionalJs = array('js/universal.js','js/battleship.js','js/playerBoard.js','js/enemyBoard.js');
    echo includeFooter($additionalJs);
?>
