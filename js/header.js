$(document).ready(function() {
    $(document).on('click','#logout',()=> {
      //send logout command
        $.get(`${routerEndPoint}logout`, (data) => {
            if(data.status == 200) {
                //remove the user from localstorage
                localStorage.removeItem('battleuser');
                //refresh
                location.reload();
            }else{
                showMessage(data.message);
            }
        }, 'json');
    })
});