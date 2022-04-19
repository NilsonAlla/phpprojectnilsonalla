
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nilsi | Register</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/register.css" rel="stylesheet">
    <link href="css/toastr.css" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">Nilsi</h1>
        </div>
        <h3>Register </h3>

        <form id="register-form" class="m-t" role="form" action="" method="post">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="firstname"  name="firstname"
                       id="firstname">
                <p id="errors" class="errors">

                </p>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Surname"  name="lastname"
                       id="lastname" >
                <p id="errors" class="errors">

                </p>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email"  name="email" id="email"
                       >
                <p id="errors" class="errors">

                </p>
            </div>
            <div class="form-group">
                <input type="text" autocomplete="off" class="form-control" placeholder="Birthday"
                       name="birthday" id="birthday" value="">
                <p id="errors" class="errors">
                   
                </p>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password"  name="password"
                       id="password">
                <p id="errors" class="errors">

                </p>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Confirm Password"
                       name="confirm_password" id="confirm_password">
                <p id="errors" class="errors">

                </p>
            </div>
            <div class="form-group">
                <div class="checkbox i-checks"><label> <input id="terms" type="checkbox" name="terms" value="1"><i></i>
                        Agree the terms and policy
                    </label></div>
            </div>
            <p id="terms-error" class="errors">

            </p>
            <button type="submit" name="submit" id="submit" class="btn btn-primary block full-width m-b" value="1">
                Register
            </button>

            <p class="text-muted text-center"><small>Already have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
        </form>
        <p class="m-t"><small>Created by Nilsi &copy; 2022</small></p>
    </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/toastr.js"></script>
<!-- iCheck -->
<script src="js/plugins/iCheck/icheck.min.js"></script>
<!-- Daterangepicker -->
<script src="js/plugins/fullcalendar/moment.min.js"></script>
<script src="js/plugins/daterangepicker/daterangepicker.js"></script>
<script>

    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{5,20})+$/;
    var error="";
    var fname = document.getElementById("firstname");
    var lname = document.getElementById("lastname");
    var email = document.getElementById("email");
    var bthday = document.getElementById("birthday");
    var pssword = document.getElementById("password");
    var cpassword = document.getElementById("confirm_password");
    var terms  =  document.getElementById('terms');



    function isAlphaOrParen(str) {
        return /^[a-zA-Z()]+$/.test(str);
    }

    $(function () {
        $('#register-form').submit(function (e) {
            e.preventDefault()
            let _this = $(this)

            clear(_this)
            let data = {
                firstname: _this.find('#firstname').val(),
                lastname: _this.find('#lastname').val(),
                email: _this.find('#email').val(),
                birthday: _this.find('#birthday').val(),
                password: _this.find('#password').val(),
                confirm_password: _this.find('#confirm_password').val(),
                terms: _this.find('#terms').is(":checked"),
                action: 'register'
            }

            // Validates
            var lowerCaseLetters = /[a-z]/g;
            var upperCaseLetters = /[A-Z]/g;
            var numbers = /[0-9]/g;

            let errors = {};

            // First name validate
            if (!data.firstname) {
                errors['firstname'] = 'First name is required.'
            }else if (data.firstname.length < 3){
                errors['firstname'] = 'First name need at least 3 char. '
            }else if (!isAlphaOrParen(data.firstname)){
                errors['firstname'] ='you cant use number or space for First name . '
            }

            // Lastname validate
            if (!data.lastname) {
                errors['lastname'] = 'First name is required.'
            }else if (data.lastname.length < 3){
                errors['lastname'] = 'Last name need at least 3 char. '
            }else if (!isAlphaOrParen(data.lastname)){
                errors['lastname'] ='you cant use number or space for Last name . '
            }

            // Email Validate
            if(!ValidateEmail(email) ){
                errors['email'] = 'email is not valid'
            }else if (!data.email){
                errors['email'] = 'email is required'
            }

            // Date Validate
            var date0 = new Date()
            var date1 = new Date (_this.find('#birthday').val());
            var Difference_In_Time = date0.getTime() - date1.getTime();
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            if(Difference_In_Days / 365 <18){
                errors['birthday']= 'cant regist if you are under 18 years old'
            }else if (!data.birthday){
                errors['birthday'] = 'Birthday is required'
            }

            // Password Validate
            if(!pssword.value.match(lowerCaseLetters)) {
                errors['password'] = 'password requred at least 1 lower letter'
            }else if( !pssword.value.match(upperCaseLetters)) {
                errors['password'] = 'password requred at least 1 uper letter'
            }else if (!pssword.value.match(numbers)) {
                errors['password'] = 'password requred at least 1 number'
            }else if (pssword.value.length <8){
                errors['password'] = 'password requred at least 1 8 char'
            }else if (!data.password){
                errors['password'] = 'Password is required'
            }

            // Dconfirm password Validate
            if (data.password != data.confirm_password ){
                errors['confirm_password'] = 'confirm password dont match with password'
            }else if (!data.confirm_password){
                errors['confirm_password'] = 'confirm password is required'
            }

            // Terms validate
            if( !terms.checked ){
                errors['terms'] = 'please accept terms and policy to continue'
            }


            // Create new user to db
            if (Object.keys(errors).length) {
                showValidationErrors(_this, errors)
                toastr.error('Fushat nuk jane plotesuar ne formatin e duhur.')
                return;
            }else {

                $.ajax({
                    url: 'register.php',
                    data: data,
                    type: 'POST'
                }).then(function (response) {
                    window.location.href = 'http://localhost/nilsi/WD1/login.php';
                }).catch(function (response) {
                    if (response.status === 422) {
                        showValidationErrors(_this, response.responseJSON.errors)
                    }
                    toastr.error(response.responseJSON.message)
                })
            }
        })
    })


    function clear(element)
    {
        element.find('.is-invalid').removeClass('is-invalid')
        element.find('.invalid-feedback').remove();
    }

    function showValidationErrors(parentElement, errors) {
        $.each(errors, function (index, value) {
            let element = parentElement.find(`[name=${index}]`)
            element.addClass('is-invalid')
            element.closest('.form-group').append(`<span style="display: block !important;" class="invalid-feedback">${value}</span>`)
        })
    }

    //validate email function

    function ValidateEmail(email) {

        var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

        if (email.value.match(validRegex)) {
            return true;

        } else {

            return false


        }

    }


    // When the user starts to type something inside the password field


    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $("#birthday").daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10),

        }, function(start, end, label) {


        });




    });

    function checkForm(form)
    {
        // regular expression to match required date format
        re = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;

        if(form.startdate.value != '') {
            if(regs = form.startdate.value.match(re)) {
                // day value between 1 and 31
                if(regs[1] < 1 || regs[1] > 31) {
                    alert("Invalid value for day: " + regs[1]);
                    form.startdate.focus();
                    return false;
                }
                // month value between 1 and 12
                if(regs[2] < 1 || regs[2] > 12) {
                    alert("Invalid value for month: " + regs[2]);
                    form.startdate.focus();
                    return false;
                }
                // year value between 1902 and 2022
                if(regs[3] < 1902 || regs[3] > (new Date()).getFullYear()) {
                    alert("Invalid value for year: " + regs[3] + " - must be between 1902 and " + (new Date()).getFullYear());
                    form.startdate.focus();
                    return false;
                }
            } else {
                alert("Invalid date format: " + form.startdate.value);
                form.startdate.focus();
                return false;
            }
        }

        // regular expression to match required time format
        re = /^(\d{1,2}):(\d{2})([ap]m)?$/;

        if(form.starttime.value != '') {
            if(regs = form.starttime.value.match(re)) {
                if(regs[3]) {
                    // 12-hour value between 1 and 12
                    if(regs[1] < 1 || regs[1] > 12) {
                        alert("Invalid value for hours: " + regs[1]);
                        form.starttime.focus();
                        return false;
                    }
                } else {
                    // 24-hour value between 0 and 23
                    if(regs[1] > 23) {
                        alert("Invalid value for hours: " + regs[1]);
                        form.starttime.focus();
                        return false;
                    }
                }
                // minute value between 0 and 59
                if(regs[2] > 59) {
                    alert("Invalid value for minutes: " + regs[2]);
                    form.starttime.focus();
                    return false;
                }
            } else {
                alert("Invalid time format: " + form.starttime.value);
                form.starttime.focus();
                return false;
            }
        }

        alert("All input fields have been validated!");
        return true;
    }
</script>
</body>

</html>