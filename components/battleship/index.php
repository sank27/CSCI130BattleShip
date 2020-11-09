<?php
include_once('components/functions.php');
$additionalCss = array('css/universal.css','css/login.css');
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
                        <h4 class="mt-3 text-right mr-5">Login</h4>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" method="post" action="router/router.php?request=login" class="mt-3">
                        <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
                        <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
                        <button type="button" id="submitLogin" class="login-button fadeIn fourth">Log In</button>
                    </form>
                    <div class="response ">
                    </div>
                    <div class="success alert alert-success hidden">
                        Login Successful!
                    </div>
                    <!-- Remind Passowrd -->
                    <div id="formFooter" class="mt-2">
                        <div class="row">
                            <div class="col">
                                <a class="underlineHover" href="#">Forgot Password?</a>
                            </div>
                            <div class="col">
                                <a class="underlineHover" href="register.php">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
$additionalJs = array('js/login.js');
echo includeFooter($additionalJs);
?>