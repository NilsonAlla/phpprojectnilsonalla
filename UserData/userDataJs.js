
    $('#userNav').addClass('active');


    $(document).ready(function () {

        var dt = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            "bDestroy": true,
            ajax: {
                url: "backend/adminbackend.php",
                data: {
                    action: "server_side_list",
                    urlData: urlData

                }
            },
            columns: [
                {data: "id"},
                {data: "firstname"},
                {data: "lastname"},
                {data: "email"},
                {data: "birthday"},
                {data: "post"},
                {data: "action"}


            ],

            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {
                    extend: 'excel',
                    className: "buttonsToHide",
                    exportOptions: {
                        // columns: ':visible:not(:eq(0))',
                    },
                    title: 'Data',
                    text: 'Excel',
                }
            ],
            oLanguage: {
                buttons: {
                    copyTitle: 'Copia to appunti',
                    copySuccess: {
                        _: 'Copiato %d rige to appunti',
                        1: 'Copiato 1 riga'
                    }
                },
                sLengthMenu: 'Show <select>' +
                    '<option value="10">10</option>' +
                    '<option value="30">30</option>' +
                    '<option value="50">50</option>' +
                    '<option value="-1">All</option>' +
                    '</select> records',

                oPaginate: {
                    sFirst: "first" ,
                    sNext: "next" ,
                    sLast: "last" ,
                    sPrevious: "previous"
                }
            }
        });


    });


    $('#datefilter').daterangepicker({
        "showDropdowns": true,
        "startDate": "02/24/2022",
        "endDate": "03/02/2022"
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });


    $('#select2name').select2({
        placeholder: 'Name',
        class: 'form-control'
    });

    $('#select2email').select2({
        placeholder: 'Email',
        class: 'form-control'
    });

    $(function () {
        $('#filters').submit(function (event) {
            event.preventDefault();
            let _this = $(this)


            var dt = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                serverMethod: 'POST',
                "bDestroy": true,
                ajax: {
                    url: "backend/adminbackend.php",
                    data: {
                        action: "server_side_list",
                        email: _this.find('#select2email').val(),
                        fullname: _this.find('#select2name').val(),
                        date: _this.find('#datefilter').val(),
                        urlData: "<?= $_GET['ids'] ?>"
                    }
                },
                columns: [
                    {data: "id"},
                    {data: "firstname"},
                    {data: "lastname"},
                    {data: "email"},
                    {data: "birthday"},
                    {data: "post"},
                    {data: "action"}


                ],

                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {
                        extend: 'excel',
                        className: "buttonsToHide",
                        exportOptions: {
                            columns: ':visible:not(:eq(0))',
                        },
                        title: 'Data',
                        text: 'Excel',
                    }
                ],
                oLanguage: {
                    buttons: {
                        copyTitle: 'Copia to appunti',
                        copySuccess: {
                            _: 'Copiato %d rige to appunti',
                            1: 'Copiato 1 riga'
                        }
                    },
                    sLengthMenu: 'Show <select>' +
                        '<option value="10">10</option>' +
                        '<option value="30">30</option>' +
                        '<option value="50">50</option>' +
                        '<option value="-1">All</option>' +
                        '</select> records',

                    oPaginate: {
                        sFirst: "first" ,
                        sNext: "next" ,
                        sLast: "last" ,
                        sPrevious:   "previous"
                    }
                }
            });


            // .....

        })


    })

    function fillmodal(id) {


        $.ajax({
            type: 'post',
            url: 'backend/adminbackend.php',
            data: {
                action: "keepuserdata",
                id: id
            },
            success: function (response) {

                response = JSON.parse(response)
                data = response['data']

                $('#usereditid').val(data.usereditid);
                $('#first_name_modal').val(data['firstname']);
                $('#last_name_modal').val(data.lastname);
                $('#email_modal').val(data.email);
                $('#birthday_modal').val(data.birthday);
                $('#post_modal').val(data.post);
                let photo = data.photopath;
                console.log(photo);
                $('#photoEditShowModal').attr("src", photo);

            }
        })


    }

    // marrja e te dhenave te user update nga modali dhe dergimi ne back
    $(function () {
        $('#updateuser').submit(function (event) {
            event.preventDefault();
            let _this = $(this);
            let errors = {};
            clear(_this);

            var photoExist = $('#photoEditShowModal').attr('src');
            var photo = $('#photo_modal')[0].files[0];
            var data = new FormData();

            data.append("action", "update");
            data.append("usereditid", $('#usereditid').val());
            data.append("firstname", $('#first_name_modal').val());
            data.append("lastname", $('#last_name_modal').val());
            data.append("email", $('#email_modal').val());
            data.append("birthday", $('#birthday_modal').val());
            data.append("post_modal", $('#post_modal').val());
            data.append("photo_modal", photo);


            // validimi i te te dhenave te marra nga inputs form


            // First name validate
            if (!data.get('firstname')) {
                errors['first_name_modal'] = 'First name is required.'
            } else if (data.get('firstname').length < 3) {
                errors['first_name_modal'] = 'First name need at least 3 char. '
            } else if (!isAlphaOrParen(data.get('firstname'))) {
                errors['first_name_modal'] = 'you cant use number or space for First name . '
            }

            // Lastname validate
            if (!data.get('lastname')) {
                errors['last_name_modal'] = 'First name is required.'
            } else if (data.get('lastname').length < 3) {
                errors['last_name_modal'] = 'Last name need at least 3 char. '
            } else if (!isAlphaOrParen(data.get('lastname'))) {
                errors['last_name_modal'] = 'you cant use number or space for Last name . '
            }

            // Email Validate
            let email = data.get('email')

            if (!data.get('email')) {
                errors['email_modal'] = 'email is required'
            } else if (!ValidateEmail(email)) {
                errors['email_modal'] = 'email is not valid'
            }

            // Date Validate
            var date0 = new Date()
            var date1 = new Date(data.get('birthday'));
            var Difference_In_Time = date0.getTime() - date1.getTime();
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            if (Difference_In_Days / 365 < 18) {
                errors['birthday_modal'] = 'cant regist if you are under 18 years old'
            } else if (!data.get('birthday')) {
                errors['birthday_modal'] = 'Birthday is required'
            }


            // validimi i rolit


            if (data.get('post_modal') == 'null') {
                errors['post_modal'] = "Role is required"

            }

            // validimi i fotos
            if (!photoExist) {
                var foto = "image/jpeg"

                if (!data.get('photo_modal').name) {
                    errors['photo_modal'] = "Photo is required";
                } else if (!data.get('photo_modal').name) {
                    data.append("photo_modal", photoExist);
                } else if (data.get('photo_modal').type !== foto || data.get('photo_modal').type !== foto || data.get('photo_modal').type !== foto || data.get('photo_modal').type !== foto) {
                    errors['photo_modal'] = "upload photo only";
                }
            }
            // dergimi per update nese nuk ka errore
            if (Object.keys(errors).length) {
                showValidationErrors(_this, errors)
                toastr.error('Fushat nuk jane plotesuar ne formatin e duhur.');

            } else {

                $.ajax({
                    type: 'post',
                    url: 'backend/adminbackend.php',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: data,
                    success: function (response) {
                        $('#updateModal').modal('toggle');
                        var table = $('#datatable').DataTable();
                        table.draw(true);
                    },

                    error: function (response) {
                        errors = response.responseJSON.data
                        if (response.status === 422) {

                            showValidationErrors(_this, errors)
                        }
                    }

                })
            }
        })

    })


    // mbushja e modalit te delete user me te dhena
    function deleteusermodal(id) {

        $.ajax({

            type: 'post',
            url: 'backend/adminbackend.php',
            data: {
                action: "keepuserdata",
                id: id
            },
            success: function (response) {

                response = JSON.parse(response)
                data = response['data']

                $('#userdeleteid').val(id);
                $('#firstnamedeletemodal').val(data['firstname']);
                $('#lastnamedeletemodal').val(data.lastname);
                $('#emaildeletemodal').val(data.email);
                $('#birthdaydeletemodal').val(data.birthday);
                $('#postdeletemodal').val(data.post);
            }

        })

    }

    $(function () {
        $('#deleteuser').submit(function (event) {
            event.preventDefault();

            var data = new FormData();
            data.append("id", $('#userdeleteid').val());
            data.append("action", "deleteuser");


            $.ajax({

                type: 'post',
                url: 'backend/adminbackend.php',
                contentType: false,
                cache: false,
                processData: false,
                data: data,
                success: function (response) {
                    $('#deleteModal').modal('toggle');
                    let table = $('#datatable').DataTable();
                    table.draw(true);
                }
            })


        })
    })

    function isAlphaOrParen(str) {
        return /^[a-zA-Z()]+$/.test(str);
    }


    // funksion qe kontrollon nese te dhenat jane letters only
    function isAlphaOrParen(str) {
        return /^[a-zA-Z()]+$/.test(str);
    }


    // funxion per mos lejimin e perseritjes ne front e te njejtit error
    function clear(element) {
        element.find('.is-invalid').removeClass('is-invalid')
        element.find('.invalid-feedback').remove();
    }

    // shfaqa e erroreve ne front
    function showValidationErrors(parentElement, errors) {
        console.log(errors);
        $.each(errors, function (index, value) {
            let element = parentElement.find(`[name=${index}]`)
            element.addClass('is-invalid')
            element.closest('.form-group').append(`<span style="display: block !important;" class="invalid-feedback">${value}</span>`)
        })
    }


    // funksioni i dates
    $('#birthday_modal').daterangepicker({
        "singleDatePicker": true,
        "showDropdowns": true,
        "startDate": "01/12/2022",
        "endDate": "01/18/2022"
    }, function (start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    $('#birthdayuseradd').daterangepicker({
        "singleDatePicker": true,
        "showDropdowns": true,
        "startDate": "01/12/2022",
        "endDate": "01/18/2022"
    }, function (start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });


    //validate email function

    function ValidateEmail(email) {

        var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

        if (1 == 1) {
            return true;

        } else {

            return false


        }

    }

    function employerRedirect() {
        window.location.href = "employerinterface.php"
    }

    function userRedirect() {
        window.location.href = "userData.php"

    }

    //  funksioni i cili sherben per te bere logout adminin
    $(function () {
        $('#logout-form').submit(function (event) {
            event.preventDefault();

            var data = new FormData();

            data.append("action", "logout");

            $.ajax({
                type: 'post',
                url: 'logout.php',
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function () {
                    window.location.href = '../login.php';
                }
            })

        })
    })


