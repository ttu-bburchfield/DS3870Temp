$(document).on('click','#btnSignIn',function(){
    console.log('btnSignIn Clicked');
    let strUsername = '';
    strUsername = $('#txtEmail').val();
    console.log('Set value of strUsername = ' + strUsername);
    localStorage.setItem('Username',strUsername);
    console.log('Set localStorage Username');
    console.log($('#txtPassword').val());


    if($('#txtEmail').val() && $('#txtPassword').val()){
        console.log('Username and Password exists');
    } else {
        console.log('Username or Password Blank');
    }
})