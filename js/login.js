$(document).ready(function() {
    $(document).on('click', '#submitLogin',function(e){ login() });
});

function login() {
    //get the form information
    const login = $('#login').val();
    const password = $('#password').val();

    $.post("router/router.php?request=login",
        {
            login,
            password
        },function(data, status){
            console.log(data);
            console.log(status);
        });
}