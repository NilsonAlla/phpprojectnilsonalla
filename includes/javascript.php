<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/inspinia.js"></script>
<script src="js/plugins/daterangepicker/daterangepicker.js"></script>
<script src="js/plugins/pace/pace.min.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
<script src="js/plugins/dataTables/datatables.min.js"></script>

<script>
    $('#raportNav').addClass('active');


    function formatYear(tableid) {
        return `<table id="${tableid}">
                <thead>
                    <tr role="row">
                        <th></th>
                        <th>Year</th>
                        <th>Total hours in</th>
                        <th>Total hours out</th>
                        <th>Total hours</th>
                    </tr>
                </thead>
            </table>`
    }

    function formatmonths(tableid) {
        return `<table id="${tableid}">
                <thead>
                    <tr role="row">
                        <th></th>
                        <th>month</th>
                        <th>Total hours in</th>
                        <th>Total hours out</th>
                        <th>Total hours</th>
                    </tr>
                </thead>
            </table>`
    }

    function formatdays(tableid) {
        return `<table id="${tableid}">
                <thead>
                    <tr role="row">
                        <th>days</th>
                        <th>Total hours in</th>
                        <th>Total hours out</th>
                        <th>Total hours </th>
                    </tr>
                </thead>
            </table>`
    }

    $(document).ready(function () {
        let dt = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            ajax: {
                url: "backend/employerbackend.php",
                data: {
                    action: "employerlist"
                }
            },
            columns: [
                {
                    "class": "details-control main-table-details",
                    "orderable": false,
                    "data": null,
                    "defaultContent": "<i class='fas fa-plus-circle text-success'></i>"
                },
                {
                    "data": "nr",
                    "name": "nr",
                    searchable: false,
                    orderable: false
                },
                {"data": "firstname", "name": "firstname"},
                {"data": "lastname", "name": "lastname"},
                {
                    data: "first_date",
                    name: "first_date",
                    searchable: false,
                    orderable: false

                },
                {
                    data: "last_date",
                    name: "last_date",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "hours",
                    name: "hours",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "overtime",
                    name: "overtime",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "total",
                    name: "total",
                    searchable: false,
                    orderable: false
                }
            ],
            "order": [[2, 'asc']],
            oLanguage: {
                sLengthMenu: 'Show <select>' +
                    '<option value="10">10</option>' +
                    '<option value="30">30</option>' +
                    '<option value="50">50</option>' +
                    '<option value="-1">All</option>' +
                    '</select> records',

                oPaginate: {
                    sFirst: "first",
                    sNext: "next",
                    sLast: "last",
                    sPrevious: "previous"
                }
            }
        });

        // Array to track the ids of the details displayed rows
        var detailRows = [];

        var yearsDetailsTables = {}
        var monthsDetailsTables = {}
        var daysDetailsTables = {}
        $('#datatable tbody').on('click', 'tr td.main-table-details', function () {
            let tr = $(this).closest('tr');
            let row = dt.row(tr);
            let idx = $.inArray(tr.attr('id'), detailRows);
            let data = row.data()
            let detailsTableId = `total-hours-table-${data.id}`

            if (row.child.isShown()) {
                tr.find('.fa-minus-circle').replaceWith("<i class='fas fa-plus-circle text-success'></i>")

                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
                yearsDetailsTables[detailsTableId].destroy()
            } else {
                tr.find('.fa-plus-circle ').replaceWith("<i class='fas fa-minus-circle text-danger'></i>")
                tr.addClass('details ');
                row.child(formatYear(detailsTableId)).show();
                let years = [];
                $.each(data.year, function (index, value) {
                    years.push({
                        year: index,
                        hours: parseFloat(value.hours).toFixed(2),
                        overtime: parseFloat(value.overtime).toFixed(2),
                        total: parseFloat(value.total).toFixed(2),
                        months: value.month
                    })
                })
                yearsDetailsTables[detailsTableId] = $(`#${detailsTableId}`).DataTable({
                    data: years,
                    columns: [
                        {
                            "class": "details-control years-details-table",
                            "orderable": true,
                            "search": false,
                            "data": null,
                            "defaultContent": "<i class='fas fa-plus-circle text-success'></i>"
                        },
                        {'data': 'year'},
                        {'data': 'hours'},
                        {'data': 'overtime'},
                        {'data': 'total'},
                    ]
                });
                yearsDetailsTables[detailsTableId].on('click', 'tr td.years-details-table', function () {
                    let tr = $(this).closest('tr');
                    let row = yearsDetailsTables[detailsTableId].row(tr);
                    let idx = $.inArray(tr.attr('id'), detailRows);
                    let data = row.data();
                    // console.log(data.year);
                    let monthDetailTableId = `month-details-table-${data.year}-${detailsTableId}`
                    if (row.child.isShown()) {
                        tr.find('.fa-minus-circle').replaceWith("<i class='fas fa-plus-circle text-success'></i>")

                        tr.removeClass('details');
                        row.child.hide();

                        // Remove from the 'open' array
                        detailRows.splice(idx, 1);
                        monthsDetailsTables[monthDetailTableId].destroy()
                    } else {
                        tr.find('.fa-plus-circle ').replaceWith("<i class='fas fa-minus-circle text-danger'></i>")
                        tr.addClass('details ');
                        row.child(formatmonths(monthDetailTableId)).show();
                        let months = [];
                        $.each(data.months, function (index, value) {

                            console.log(data.months);
                            months.push({
                                month: index,
                                hours: parseFloat(value.hours).toFixed(2),
                                overtime: parseFloat(value.overtime).toFixed(2),
                                total: parseFloat(value.total).toFixed(2),
                                day: value.day
                            })
                        })
                        monthsDetailsTables[monthDetailTableId] = $(`#${monthDetailTableId}`).DataTable({
                            data: months,
                            columns: [
                                {
                                    "class": "details-control months-details-table",
                                    "orderable": true,
                                    "data": null,
                                    "defaultContent": "<i class='fas fa-plus-circle text-success'></i>"
                                },
                                {'data': 'month'},
                                {'data': 'hours'},
                                {'data': 'overtime'},
                                {'data': 'total'},
                            ]
                        });

                        monthsDetailsTables[monthDetailTableId].on('click', 'tr td.months-details-table', function () {


                            let tr = $(this).closest('tr');
                            let row = monthsDetailsTables[monthDetailTableId].row(tr);
                            let idx = $.inArray(tr.attr('id'), detailRows);
                            let data = row.data();

                            let dayDetailTableId = `days-details-table-${data.month}-${monthDetailTableId}`

                            if (row.child.isShown()) {
                                tr.find('.fa-minus-circle').replaceWith("<i class='fas fa-plus-circle text-success'></i>")

                                tr.removeClass('details');
                                row.child.hide();

                                // Remove from the 'open' array
                                detailRows.splice(idx, 1);
                                daysDetailsTables[dayDetailTableId].destroy()
                            } else {
                                tr.find('.fa-plus-circle ').replaceWith("<i class='fas fa-minus-circle text-danger'></i>")
                                tr.addClass('details ');
                                row.child(formatdays(dayDetailTableId)).show();
                                let days = [];

                                $.each(data.day, function (index, value) {

                                    // console.log(index);
                                    days.push({
                                        day: index,
                                        hours: value.hours,
                                        overtime: value.overtime,
                                        total: value.total
                                    })
                                })
                                daysDetailsTables[dayDetailTableId] = $(`#${dayDetailTableId}`).DataTable({
                                    data: days,
                                    columns: [
                                        {'data': 'day'},
                                        {'data': 'hours'},
                                        {'data': 'overtime'},
                                        {'data': 'total'},
                                    ]
                                });

                            }
                        })
                    }
                })

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }
        });

        // year table

        $('.yeartable tbody').on('click', 'tr td.details-control', function () {
            console.log('hello');
            var tr = $(this).closest('tr');
            var row = dt.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.find('.fa-minus-circle').replaceWith("<i class='fas fa-plus-circle text-success'></i>")

                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                tr.find('.fa-plus-circle ').replaceWith("<i class='fas fa-minus-circle text-danger'></i>")


                tr.addClass('details ');
                row.child(formatMonth(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }
        });

        // On each draw, loop over the `detailRows` array and show any child rows
        dt.on('draw', function () {
            $.each(detailRows, function (i, id) {
                $('#' + id + ' td.details-control').trigger('click');
            });
        });
    });

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
                    window.location.href = 'login.php';
                }
            })

        })
    })

</script>