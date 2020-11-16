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
        <div class="col-9" id="chat">
            CHAT HERE
        </div>
        <div class="col-3" id="opponents">

        </div>
    </div>
</div>

<div id="messageModal" class="modal" tabindex="-1" role="dialog">
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
$additionalJs = array('js/universal.js', 'js/home.js');
echo includeFooter($additionalJs);
?>
