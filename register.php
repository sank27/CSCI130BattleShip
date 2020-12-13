<?php
include_once('components/functions.php');
$additionalCss = array('css/register.css');
echo includeHeader($additionalCss);
?>
    <div class="container">
        <div class="row">
            <div class="wrapper fadeInDown">
                <div id="formContent">
                    <!-- Tabs Titles -->

                    <!-- Icon -->
                    <div class="fadeIn first mt-3">
                        <i class="fas fa-user-plus fa-7x"></i>
                        <h4 class="mt-3 text-right mr-5">REGISTER</h4>
                    </div>

                    <!-- Login Form -->
                    <form id="registerForm" method="post" action="router/router.php?request=login" class="mt-3">
                        <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
                        <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
                        <button type="button" id="submitRegister" class="register-button fadeIn fourth">Register</button>
                    </form>
                    <div class="response ">
                    </div>
                    <div class="success alert alert-success hidden">
                        Successfully Registered! <a href="index.php">Go to Login Page!</a>
                    </div>
                    <!-- Remind Passowrd -->
                    <div id="formFooter" class="mt-2">
                        <div class="row">
                            <div class="col">
                                <a class="underlineHover" href="#">Forgot Password?</a>
                            </div>
                            <div class="col">
                                <a class="underlineHover" href="index.php">Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
$additionalJs = array('js/register.js');
echo includeFooter($additionalJs);
?>