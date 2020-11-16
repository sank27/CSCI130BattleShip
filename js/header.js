$(document).ready(function() {
    $(document).on('click','#logout',()=> {
      //send logout command
        $.get("router/router.php?request=logout");
        //remove the user from localstorage
        localStorage.removeItem('battleuser');
        //refresh
        location.reload();
    })
});