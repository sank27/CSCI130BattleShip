$(document).ready(function() {
    $(document).on('click','#logout',()=> {
      //send logout command
        $.get("router/router.php?request=logout");
        //refresh
        location.reload();
    })
});