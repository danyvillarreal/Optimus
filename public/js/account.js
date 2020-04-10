function updateLocation(e) {
    $('#spinner').show();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var formData = $('#formLocation-'+e.value).serialize() + "&id=" + e.value;
    $.ajax({
        url: urlUpdateLocation,
        method: 'post',
        data: formData,
        success: function(result) {
            if (result.success) {
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
            $('#spinner').hide();
        }
    });
}
function insertLocation(e) {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var formData = $('#newLocation').serialize() + "&account_id=" + e.value;
    $.ajax({
        url: urlInsertLocation,
        method: 'post',
        data: formData,
        success: function(result) {
            if (result.success) {
                showSnackbar(result.data.message);
                $('#listLocations').html(result.data.html);
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}
function deleteLocation(e) {
    $('#spinner').show();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlDeleteLocation,
        method: 'post',
        data: {
            id: e.value,
        },
        success: function(result) {
            if (result.success) {
                showSnackbar(result.data.message);
                $('#listLocations').html(result.data.html);
            } else {
                showSnackbarError(result.data.message);
            }
            $('#spinner').hide();
        }
    });
}
function showSnackbar(msg) {
    var x = document.getElementById("snackbar");
    x.className = "show";
    x.innerHTML = msg;
    setTimeout(function(){
        x.className = x.className.replace("show", "");
    }, 2000);
}
function showSnackbarError(msg) {
    var x = document.getElementById("snackbarError");
    x.className = "show";
    x.innerHTML = msg;
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);
}