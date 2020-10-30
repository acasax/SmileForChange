const checkJWT = ()=>{
    let jwt = localStorage.getItem('token')
    if(jwt){
        $.ajax({
            url: "app/ajax/checkJWT.php",
            type: "POST",
            data: {
                jwt: jwt,
            },
            dataType: "JSON",
            success: function success(data) {
                if(data){
                    if(data.type && data.type === "LOGOUT"){
                        window.location.replace('../login.html')
                        return;
                    }
                    $('#userSrc').attr('src',data.userSrc);
                    $('#userName').text(data.userName);
                }
            },
            error: function error() {

            }
        })
    }  else{
        logout()
        window.location.replace('../login.html');
    }
}

function page_loader() {
    $('.loading-area').fadeOut(2000)
};


const logout = ()=>{
    localStorage.removeItem('token');
}
checkJWT();

$(document).on('click','#logout',function(){
    logout()
})


jQuery(window).on('load', function () {
    page_loader();
})