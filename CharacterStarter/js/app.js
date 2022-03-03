// Begin Page Specific Functions

$(document).on('click','#btnAddAccount', function(){

})

$(document).on('click','#btnAddCharacter', function(){

})

$(document).on('click','#btnLogin', function(){
    let blnError = false;
    let strErrorMessage = '';
    if(!$('#txtEmail').val()){
        blnError = true;
        strErrorMessage += '<p>Please provide an email address to continue</p>';
    }
    if(!$('#txtPassword').val()){
        blnError = true;
        strErrorMessage += '<p>Please provide your password to continue</p>';
    }
    if(blnError == true){
        Swal.fire({
            icon: 'error',
            title: 'Missing Data',
            html: strErrorMessage
        }) 
    } else{
        $.post('https://www.swollenhippo.com/DS3870/Comics/createSession.php',{strEmail:$('#txtEmail').val(),strPassword:$('#txtPassword').val()},function(result){
        objResult = JSON.parse(result);    
        if(objResult.Outcome != 'Login Failed'){
                // set your Session Storage Item here

                // then redirect the user to the dashboard
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    html: '<p>The provided username and password did not match any in our database</p>'
                })
            }
        })
    }
})

$(document).on('click','#btnToggleExisting', function(){

})


function clearCreateAccountPage(){
    $('#txtFirstName').val('');
    $('#txtLastName').val('');
    $('#txtEmail').val('');
    $('#txtPassword').val('');
    $('#txtVerifyPassword').val('');
}

function clearCharacterFields(){
    $('#txtCharacterName').val('');
    $('#txtSuperPower').val('');
    $('#txtLocation').val('');
    $('#selectStatus').val('Active').trigger('change');
}

function fillCharacterTable(){
    $('#tblCharacters tbody').empty();
    let strCurrentSessionID = sessionStorage.getItem('CharacterSession');
    $.getJSON('https://www.swollenhippo.com/DS3870/Comics/getCharacters.php',{strSessionID:strCurrentSessionID},function(result){
        $.each(result,function(i,superhero){
            let strTableHTML = '<tr><td>' + superhero.Name + '</td><td>' + superhero.SuperPower + '</td><td>' + superhero.Location + '</td><td>' + superhero.Status + '</td></tr>';
            $('#tblCharacters tbody').append(strTableHTML);
        })
    })
}

// End Page Specific Functions


// Begin Helper Functions
function isValidEmail(strEmailAddress){
    let regEmail = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
    return regEmail.test(strEmailAddress);
}

function isValidPassword(strPassword){
    let regPassword = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,64}$/;
    return regPassword.test(strPassword);
}

function doPasswordsMatch(strPassword, strVerifyPassword){
    if(strPassword == strVerifyPassword){
        return true;
    } else {
        return false;
    }
}
// End Helper Functions

// Begin Universal Functions
function verifySession(){
    if(sessionStorage.getItem('CharacterSession')){
        let strCurrentSessionID = sessionStorage.getItem('CharacterSession')
        $.getJSON('https://www.swollenhippo.com/DS3870/Test1/verifySession.php', {strSessionID: strCurrentSessionID}, function(result){
            if(result.Outcome != 'Valid Session'){
                return false;
            } else {
                return true;
            }
        })
    } else {
        return false;
    }
}
// End Universal Functions

