<?php
    //if a person is logged in their home is the battleship page
    $currentPage = '';
    $home = 'index.php';
    if ($_SESSION['valid'] && !empty($_SESSION['user'])){
        $home = 'battleship.php';
    }
?>
<div class="container">
    <div class="row">
        <div class="col">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="<?php echo $home; ?>">Battleship</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">

                        </li>
                    </ul>
                </div>
                <a id="logout" class="btn btn-primary">Logout</a>
            </nav>
        </div>
    </div>
</div>