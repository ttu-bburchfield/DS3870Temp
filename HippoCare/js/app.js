$(document).on('click','#btnSignIn',function(){
    var objNewSessionResponse;
    let blnError = false;
    let strErrorMessage = '';
    
    if(!$('#txtEmail').val()){
        blnError = true;
        strErrorMessage += '<p>Email/Username is Blank</p>';
    } 
    
    if(validUsernameFormat($('#txtEmail').val()) == false){
        blnError = true;
        strErrorMessage += '<p>Email/Username format is not valid</p>';
    } 

    if(!$('#txtPassword').val()){
        blnError = true;
        strErrorMessage += '<p>Password is Blank</p>';
    }

    if(validPasswordFormat($('#txtPassword').val()) == false){
        blnError = true;
        strErrorMessage += '<p>Password format is not valid</p>';
    } 

    if(blnError == true){
        Swal.fire({
            icon: 'error',
            title: 'Missing Data',
            html: strErrorMessage
        })
    } else {
        var objNewSessionPromise = $.post('https://www.swollenhippo.com/DS3870/newSession.php', { strUsername:$('#txtEmail').val(), strPassword: $('#txtPassword').val() }, function(result){
            objNewSessionResponse = JSON.parse(result);
        })

        $.when(objNewSessionPromise).done(function() {
            if(objNewSessionResponse.Outcome == 'Login Failed'){
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    html: '<h3>Please review your username and password</h3>'
                }) 
            } else {
                sessionStorage.setItem('HippoSessionID',objNewSessionResponse.Outcome);
                window.location.href = 'index.html';
            }
        })
    }

    function validUsernameFormat(strUsername){
        let reg = /\S+@\S+\.\S+/;
        return reg.test(strUsername);
    }

    function validPasswordFormat(strPassword){
        let reg = /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})/;
        return reg.test(strPassword);
    }
})