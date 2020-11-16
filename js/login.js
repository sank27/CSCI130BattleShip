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
        }, function (data, status) {
            if (data.status == 200) {
                //display success
                $('#loginForm').hide();
                $('.response').hide();
                $('.success').show();

                //save the user to the localstorage
                localStorage.setItem('battleuser', JSON.stringify(data.data));

                //redirect to internal pages
                setTimeout(function () {
                    window.location.replace("home.php");
                }, 3000);
            } else {
                $('.response').addClass('alert alert-danger');
                $('.response').html(data.data);
            }
        }, 'json');
}