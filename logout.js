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