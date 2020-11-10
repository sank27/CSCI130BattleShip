$(document).ready(function() {
    $(document).on('click', '#submitRegister',function(e){ register() });
});

function register() {
    //get the form information
    const login = $('#login').val();
    const password = $('#password').val();

    $.post("router/router.php?request=register",
        {
            login,
            password
        },function(data, status){
            if (data.status == 200){
                //display success
                $('#registerForm').hide();
                $('.response').hide();
                $('.success').show();
            }else{
                $('.response').addClass('alert alert-danger');
                $('.response').html(data.data);
            }
        }, 'json');
}