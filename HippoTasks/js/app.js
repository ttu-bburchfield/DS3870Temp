$(document).on('click','#btnNewAccount',function(){
    let strMyUsername = $('#txtUsername').val();
    let strMyPassword = $('#txtPassword').val();
    $.post('https://www.swollenhippo.com/DS3870/Tasks/newAccount.php',{ strUsername: strMyUsername, strPassword: strMyPassword },function(result){
        let objResult = JSON.parse(result);
        if(objResult.Outcome == 'New User Created'){
            Swal.fire({
                icon: 'success',
                title: 'User Created',
                html: '<p>Your account was successfuly created.  Click OK to go back to login</p>'
            }).then((result)=>{
                window.location.href = 'login.html';
            })
        } else if(objResult.Outcome == 'User Already Exists'){
            Swal.fire({
                icon: 'error',
                title: 'User Not Created',
                html: '<p>Your account was not successfuly created because user already exists</p>'
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'User Not Created',
                html: '<p>Please check your username and password and then try again.</p>'
            })
        }
    })
})

$(document).on('click','#btnLogin',function(){
    let strMyUsername = $('#txtUsername').val();
    let strMyPassword = $('#txtPassword').val();
    $.post('https://www.swollenhippo.com/DS3870/Tasks/newSession.php',{ strUsername: strMyUsername, strPassword: strMyPassword },function(result){
        let objResult = JSON.parse(result);
        if(objResult.Outcome == 'Login Failed'){
            Swal.fire({
                icon: 'error',
                title: 'Username or Password is Incorrect',
                html: '<p>Verify your username and password and try again</p>'
            })
        } else {
            sessionStorage.setItem('HippoTaskID', objResult.Outcome);
            window.location.href = 'index.html';
        }
    })
})


$(document).on('click','#btnAddTask',function(){
    let strMySessionID = sessionStorage.getItem('HippoTaskID');
    $.getJSON('https://www.swollenhippo.com/DS3870/Tasks/verifySession.php', {strSessionID: strMySessionID}, function(result){
        if(result.Outcome == 'Valid Session'){
            let strMyTaskName = $('#txtTaskName').val();
            let strMyLocation = $('#txtLocation').val();
            let strMyDate = $('#txtDueDate').val();
            let strMyNotes = $('#txtNotes').val();
            $.post('https://www.swollenhippo.com/DS3870/Tasks/newTask.php',{ strSessionID: strMySessionID, strLocation:strMyLocation, strTaskName:strMyTaskName, datDueDate:strMyDate,strNotes:strMyNotes },function(result){
                let objResult = JSON.parse(result);
                if(objResult.Outcome != 'Error'){
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Task Added!',
                        showConfirmButton: false,
                        timer: 1500
                      })
                      $('#txtTaskName').val('');
                      $('#txtLocation').val('');
                      $('#txtDueDate').val('');
                      $('#txtNotes').val('');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Task Not Added',
                        html: '<p>Verify your task data and try again</p>'
                    })
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Expired Session',
                html: '<p>Oops, appears your session has expired.  Click OK to go to login</p>'
            }).then((result)=>{
                sessionStorage.removeItem('HippoTaskID');
                window.location.href = 'login.html';
            })
        }
    })
})