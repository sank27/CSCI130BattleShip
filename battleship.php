<?php
include_once('components/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-2">
            <div id="game-start">
                <div id="available-ships" class="list-group">
                </div>
                <div class="mt-4 text-center">
                    <span class="font-weight-bold">Selected Ship:</span>
                    <div id="selected-ship"></div>
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
                <div class="mt-4">
                    <button class="removeShips btn btn-danger" disabled>Remove Ship(s)</button>
                </div>
            </div>
        </div>
        <div class="col-8">
            <table id="player-table"></table>
        </div>
        <div class="col-2"></div>
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
    include_once('components/footer.php');
?>