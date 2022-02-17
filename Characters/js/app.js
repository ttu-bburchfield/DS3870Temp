// Begin Page Specific Functions
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

