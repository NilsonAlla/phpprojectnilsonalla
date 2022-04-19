<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilsi | Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/toastr.css" rel="stylesheet">
</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">Nilsi</h1>
        </div>
        <h3>Welcome to our page</h3>

        <!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
        </p>
        <p>Login in...</p>
        <form id="login" class="m-t" role="form" action="" method="post">
            <div class="form-group">
                <input type="text" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password"
                       required>
            </div>
            <button type="submit" name="submit" id="submit" class="btn btn-primary block full-width m-b" value="1">
                Login
            </button>

            <a href="#"><small>Forgot password?</small></a>
            <p class="text-muted text-center"><small>Do not have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="https://phpprojectnilsonalla.herokuapp.com/register.php">Create an account</a>
        </form>
        <p class="m-t"><small>Create by Nilsi &copy; 2022</small></p>
    </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/toastr.js"></script>
<script>

    /** On document ready */
    $(function () {
        $('#login').submit(function (e) {
            e.preventDefault()
            let _this = $(this)
            clear(_this)
            let errors = {};
            let data = {
                email: _this.find('#email').val(),
                password: _this.find('#password').val(),
                action: 'login'
            }

            if (!data.email) {
                errors['email'] = 'email is required'
            }
            if (!data.password) {
                errors['password'] = 'Password is required'


            }

            if (Object.keys(errors).length) {
                showValidationErrors(_this, errors)
                toastr.error('Fushat nuk jane plotesuar ne formatin e duhur.')

            } else {

                $.ajax({
                    url: 'authenticate.php',
                    data: data,
                    type: 'POST'
                }).then(function (response) {

                    if (response.status === 200) {
                        window.location.href = "http://localhost/nilsi/WD1/index2.php.?page=index"
                    }
                }).catch(function (response) {
                    if (response.status === 422) {
                        showValidationErrors(_this, response.responseJSON.errors)
                    }
                })
            }
        })
        var email = $("#email");

        email.onkeyup = function () {
            if (!ValidateEmail(email)) {

                toastr["error"]("this email is invalid")

                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }

            }
        }
    });

    function clear(element) {
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

    function ValidateEmail(email) {

        var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        if (email.value.match(validRegex)) {
            return true;
        } else {
            return false
        }

    }
</script>

</body>

</html>
