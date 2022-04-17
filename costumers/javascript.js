
$('#costumerNav').addClass('active');


function show(id) {
    // console.log("hello");
    $('#' + id).toggle();
    if ($('#secplus' + id).hasClass('fa-plus')) {
        console.log("hello");
        $('#secplus' + id).removeClass('fa-plus').addClass("fa-minus");
    } else {
        $('#secplus' + id).removeClass('fa-minus').addClass("fa-plus");
    }
}


function showProductTab(id) {

    $('#prodTab' + id).toggle();

    if ($('#secplustab' + id).hasClass('fa-plus')) {
        $('#secplustab' + id).removeClass('fa-plus').addClass("fa-minus");
    } else {
        $('#secplustab' + id).removeClass('fa-minus').addClass("fa-plus");
    }
}



