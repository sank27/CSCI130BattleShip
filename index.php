<?php
include_once('components/functions.php');
$additionalCss = array('css/login.css');
echo includeHeader($additionalCss);
?>
    <div class="container">
        <div class="row">
            <div class="wrapper fadeInDown">
                <div id="formContent">
                    <!-- Tabs Titles -->

                    <!-- Icon -->
                    <div class="fadeIn first mt-3">
                        <i class="fas fa-anchor fa-7x"></i>
                    </div>

                    <!-- Login Form -->
                    <form method="post" action="router/router.php?request=login" class="mt-3">
                        <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
                        <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
                        <button type="button" id="submitLogin" class="login-button fadeIn fourth">Log In</button>
                    </form>

                    <!-- Remind Passowrd -->
                    <div id="formFooter" class="mt-2">
                        <a class="underlineHover" href="#">Forgot Password?</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php
$additionalJs = array('js/login.js');
echo includeFooter($additionalJs);
?>