

    $('#chartNav').addClass('active');


    $(document).ready(function () {


        let action = $("input[name='products']:checked").val()


        $.ajax({
            url: "backend/chartsbackend.php",
            method: "POST",
            data: {
                action: 'product',
                dataType: "JSON"
            },

            success: function (ProductData) {

                var years = [];
                var totals = [];
                var productOrigin = [];
                var totalProductOrigin = [];
                var costumer = [];
                var costumerQuantity = [];

                let datas = JSON.parse(ProductData);
                let salary = datas.salary;

                for (var count = 0; count < salary.length; count++) {
                    years.push(salary[count].year);
                    totals.push(salary[count].totalSalary);
                }


                var lineData = {
                    labels: years,
                    datasets: [

                        {
                            label: 'years',
                            backgroundColor: 'rgba(26,179,148,0.5)',
                            borderColor: "rgba(26,179,148,0.7)",
                            pointBackgroundColor: "rgba(26,179,148,1)",
                            pointBorderColor: "#fff",
                            data: totals
                        }
                    ]
                };

                var lineOptions = {
                    responsive: true
                };
                var ctx = document.getElementById("lineChart").getContext("2d");
                new Chart(ctx, {type: 'line', data: lineData, options: lineOptions});


                let Categorie = [];
                let totalCategories = [];
                let categories = [];
                let kot = []


                let products = datas.product;
                for (const [key, value] of Object.entries(products)) {
                    categories.push(key)
                    kot.push(
                        {
                            label: value
                        }
                    )
                    totalCategories.push(value.total_category)
                }

                console.log(kot);

                var barData = {
                    labels: categories,
                    datasets: [
                        {
                            label: 'Shitje per kategori',
                            backgroundColor: ['rgba(220, 220, 220, 0.5)', 'rgba(135,222,110,0.86)', 'rgba(217,91,91,0.5)'],
                            pointBorderColor: "#fff",
                            data: totalCategories
                        }, {
                            label: 'Klikim per kategori',
                            backgroundColor: ['rgba(243,11,11,0.5)', 'rgba(14,39,119,0.86)', 'rgba(150,143,143,0.5)'],
                            pointBorderColor: "#fff",
                            data: [50, 20, 10]
                        }
                    ]
                };

                var barOptions = {
                    responsive: true
                };


                var ctx2 = document.getElementById("barChart").getContext("2d");
                new Chart(ctx2, {type: 'bar', data: barData, options: barOptions});


                let origin = datas.origin;
                for (var count = 0; count < origin.length; count++) {
                    productOrigin.push(origin[count].product_origin);
                    totalProductOrigin.push(origin[count].total);
                }
                var polarData = {
                    datasets: [{
                        data: totalProductOrigin,
                        backgroundColor: [
                            "#a3e1d4", "#dedede", "#b5b8cf"
                        ],
                        label: [
                            "My Radar chart"
                        ]
                    }],
                    labels: productOrigin
                };

                var polarOptions = {
                    segmentStrokeWidth: 2,
                    responsive: true

                };

                var ctx3 = document.getElementById("polarChart").getContext("2d");
                new Chart(ctx3, {type: 'polarArea', data: polarData, options: polarOptions});

                let cstm = datas.costumers;
                for (count = 0; count < cstm.length; count++) {
                    costumer.push(cstm[count].name);
                    costumerQuantity.push(cstm[count].total);
                }

                var doughnutData = {
                    labels: costumer,
                    datasets: [{
                        data: costumerQuantity,
                        backgroundColor: ["#a3e1d4", "#d4b1d1", "#b5b8cf", "#a4e3d1", "#dedede"]
                    }]
                };


                var doughnutOptions = {
                    responsive: true
                };


                var ctx4 = document.getElementById("doughnutChart").getContext("2d");
                new Chart(ctx4, {type: 'doughnut', data: doughnutData, options: doughnutOptions});


            }
        })

    });

    function show(id) {

        $('#' + id).toggle();

        if ($('#secplus' + id).hasClass('fa-plus')) {
            $('#secplus' + id).removeClass('fa-plus').addClass("fa-minus");
        } else {
            $('#secplus' + id).removeClass('fa-minus').addClass("fa-plus");
        }
    }

    function showDetail(id) {

        $('#tab' + id).toggle();

        if ($('#' + id).hasClass('fa-plus')) {
            $('#' + id).removeClass('fa-plus').addClass("fa-minus");
        } else {
            $('#' + id).removeClass('fa-minus').addClass("fa-plus");
        }


    }


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

