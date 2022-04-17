
    $('#pagatNav').addClass('active');



    function showDetail(id) {
        // $('.col-lg-12').slideUp("fast");
        $('.tableout').slideUp("fast");
        $('#tog' + id).toggle();

        if ($('#' + id).hasClass('fa-plus')) {
            $('.iconout').removeClass('fa-minus').addClass("fa-plus");

            $('#' + id).removeClass('fa-plus').addClass("fa-minus");

            // $('#secplus' + id).removeClass('fa-plus').addClass("fa-plus");
        } else {
            $('#' + id).removeClass('fa-minus').addClass("fa-plus");
        }


    }


    function show(id) {
        $('.tablein').slideUp("fast");
        $('#' + id).toggle();

        // if ( $('.icon').not( '#secplus' + id).hasClass('fa-minus')){
        // }

        if ($('#secplus' + id).hasClass('fa-plus')) {
            $('.icon').removeClass('fa-minus').addClass("fa-plus");

            $('#secplus' + id).removeClass('fa-plus').addClass("fa-minus");

            // $('#secplus' + id).removeClass('fa-plus').addClass("fa-plus");
        } else {
            $('#secplus' + id).removeClass('fa-minus').addClass("fa-plus");
        }

    }

    $('#date').daterangepicker({
        "showDropdowns": true,
        "startDate": "01/01/2022",
        "endDate": moment().endOf('month'),
        format: 'y-m-d',
        autoUpdateInput: false
    }, function (start, end, label) {
        $('#date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });


    $('#select2name').select2({
        placeholder: 'Name',
        allowClear: true,
        class: 'form-control'
    });
