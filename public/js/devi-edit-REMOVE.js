$(document).ready(function(){
    $('#ajaxSubmit').click(function(e){
        // e.preventDefault();
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlFindAccount,
            method: 'post',
            data: {
                document: $('#documento').val(),
            },
            success: function(result){
                $('#name-data').html(result.html);
        }});
    });
    $('#createAccount').click(function(e){
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlSaveAccount,
            method: 'post',
            data: {
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
                movile: $('#movile').val(),
                phone: $('#phone').val(),
            },
            success: function(result){
                $('#name-data').html(result.html);
        }});
    });
    $('#documento').keypress(function(e){
        if ($(this).val().trim() !== '') {
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: urlFindAccount,
                method: 'post',
                data: {
                    document: $(this).val(),
                },
                success: function(result){
                $('#name-data').html(result.html);
            }});
        }
    });
    // Buscar producto
    $('#product').keypress(function(e){
        if ($(this).val().trim() !== '') {
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: urlFindProduct,
                method: 'post',
                data: {
                    product: $(this).val(),
                },
                success: function(result){
                    $('#name-data-material').html(result.html);
            }});
        }
    });
    // Finish sale
    $('#finish').click(function(e){
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlFinishSale,
            method: 'post',
            data: {
                id: $(this).val(),
            },
            success: function(result){
                if (result.success) {
                    window.location.href = '/ventas';
                } else {
                    alert(result.data.message);
                }
                // alert('Account has been found');
                // $('#name-data-material').html(result.html);
                // $('.alert').show();
                // $('.alert').html(result.success);
        }});
    });
    // Add product to sale
    $('#addToSale').click(function(e){
        var ventaId = $('#finish').val();
        var cantidad = $('#cantidad').val();
        var precio = $('#precio').val();
        var descuento = $('#descuento').val();
        var descripcionCat = $('#descripcion_categoria').val();
        var detalle = $('#detalle').val();
        var catSec = $('#categoria_secundaria').val();
        // var opcional = $('#opcional').val();
        // var oferta = $('#oferta').val();
        var opcional = document.getElementById('opcional').checked;
        var oferta = document.getElementById('oferta').checked;
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlAddProduct,
            method: 'post',
            data: {
                ventaId: ventaId,
                id: $(this).val(),
                cantidad: cantidad,
                precio: precio,
                descuento: descuento,
                detalle: detalle,
                descripcionCat: descripcionCat,
                catSec: catSec,
                opcional: opcional,
                oferta: oferta,
            },
            success: function(result){
                if (result.success) {
                    $('#productDetailBody').html(result.data.html);
                    $('#ventaDescuentos').html(result.data.descuentos);
                    $('#subTotal').html(result.data.subTotal);
                    $('#total').html(result.data.total);
                    $('#impuesto').html(result.data.impuesto);
                    $('#addToSale').attr('disabled',true);
                    $('#descuento').val('');
                    $('#finish').show();
                    showSnackbar(result.data.message);
                } else {
                    alert(result.data.message);
                }
            }
        });
    });
    $('#updateDeviDetail').click(function(e){
        var detailCategory = $('#detailCategory').val();
        var productId = $('#addToSale').val();
        var cantidad = $('#cantidad').val();
        var precio = $('#precio').val();
        var descuento = $('#descuento').val();
        var detalle = $('#detalle').val();
        var descripcionCat = $('#descripcion_categoria').val();
        var categoria = $('#categoria_principal').val();
        var lugar = $('#categoria_secundaria').val();
        var opcional = document.getElementById('opcional').checked;
        var oferta = document.getElementById('oferta').checked;
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlUpdateDeviDetail,
            method: 'post',
            data: {
                id: $(this).val(),
                productId: productId,
                cantidad: cantidad,
                precio: precio,
                descuento: descuento,
                detalle: detalle,
                descripcionCat: descripcionCat,
                categoria: categoria,
                lugar: lugar,
                opcional: opcional,
                oferta: oferta,
                detailCategory: detailCategory,
            },
            success: function(result){
                if (result.success) {
                    $('#productDetailBody').html(result.data.html);
                    $('#ventaDescuentos').html(result.data.descuentos);
                    $('#subTotal').html(result.data.subTotal);
                    $('#total').html(result.data.total);
                    $('#impuesto').html(result.data.impuesto);
                    $('#addToSale').attr('disabled',true);
                    $('#descuento').val('');
                    $('#updateDeviDetail').hide();
                    showSnackbar(result.data.message);
                } else {
                    alert(result.data.message);
                }
            }
        });
    });
});
// $("#divAgrVenta table button").click(function() {
//     var fired_button = $(this).val();
//     alert(fired_button);
//     $('#divAgrVenta').show();
//     $('#divSelectProduct').show();
// });
function findProduct(e) {
    var product = $('#product').val();
    if (product !== '') {
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlFindProduct,
            method: 'post',
            data: {
                product: product,
                cat_sec: e.value,
            },
            success: function(result){
                $('#name-data-material').html(result.html);
        }});
    }
}
function choiceAccount(e) {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlStartSale,
        method: 'post',
        data: {
            accountId: e.value,
        },
        success: function(result){
            if (result.success) {
                $('#finish').val(result.data.id);
                $('#divAgrVenta').hide();
                $('#cliente').html(result.data.cliente);
                $('#documentoCliente').html(result.data.documento);
                $('#divSelectProduct').show();
                $('#divAddProduct').show();
                $('#addToSale').attr('disabled',true);
                $('#divSaleDetail').show();
            } else {
                alert(result.data.message);
            }
        }
    });
}
function choiceProduct(e) {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlChoiceProduct,
        method: 'post',
        data: {
            id: e.value,
        },
        success: function(result){
            if (result.success) {
                $('#addToSale').val(e.value);
                $('#nombre_producto').val(result.data.info['nombre']);
                $('#codigo').val(result.data.info['codigo']);
                $('#precio').val(result.data.info['precio']);
                $('#addToSale').attr('disabled',false);
                $('#updateDeviDetail').hide();
                $('#product').val('');
                $('#name-data-material').html('');
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}
function getSecondCategory(e) {
    var ventaId = $('#finish').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlGetSecondCategory,
        method: 'post',
        data: {
            id: e.value,
            ventaId: ventaId,
        },
        success: function(result){
            $('#categoria_secundaria').html(result.data.html);
            if (result.data.info !== null) {
                $('#descripcion_categoria').val(result.data.info['description']);
            } else {
                $('#descripcion_categoria').val('');
            }
        }
    });
}

// Remove detail
function removeItem(e) {
    var ventaId = $('#finish').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlRemoveDeviDetail,
        method: 'post',
        data: {
            id: e.value,
            ventaId: ventaId,
        },
        success: function(result){
            if (result.success) {
                $('#productDetailBody').html(result.data.html);
                $('#ventaDescuentos').html(result.data.descuentos);
                $('#subTotal').html(result.data.subTotal);
                $('#total').html(result.data.total);
                $('#impuesto').html(result.data.impuesto);
                showSnackbar(result.data.message);
            } else {
                alert(result.data.message);
            }
        }
    });
}
// Edit detail
function editItem(e) {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlEditDeviDetail,
        method: 'post',
        data: {
            id: e.value,
        },
        success: function(result){
            if (result.success) {
                $('#updateDeviDetail').show();
                $('#updateDeviDetail').val(e.value);
                $('#addToSale').val(result.data.info['product_id']);
                $('#detailCategory').val(result.data.info['detailCategory']);

                $('#categoria_secundaria').html(result.data.html);
                $('#descripcion_categoria').val(result.data.info['description']);

                $('#nombre_producto').val(result.data.info['nombre']);
                $('#codigo').val(result.data.info['codigo']);
                $('#precio').val(result.data.info['precio']);
                $('#cantidad').val(result.data.info['cantidad']);
                $('#descuento').val(result.data.info['descuento']);
                $('#detalle').val(result.data.info['detalle']);
                document.getElementById('categoria_principal').value = result.data.info['categoria'];
                document.getElementById('categoria_secundaria').value = result.data.info['lugar'];
                document.getElementById('oferta').checked = false;
                document.getElementById('opcional').checked = false;
                if (result.data.info['oferta'] === 1) {
                    document.getElementById('oferta').checked = true;
                }
                if (result.data.info['opcional'] === 1) {
                    document.getElementById('opcional').checked = true;
                }
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}
// start style
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
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);
}
// end style
function initPage() {
    var id = $('#finish').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlLoadDeviDetail,
        method: 'post',
        data: {
            id: id,
        },
        success: function(result){
            if (result.success) {
                $('#productDetailBody').html(result.data.html);
                $('#ventaDescuentos').html(result.data.descuentos);
                $('#subTotal').html(result.data.subTotal);
                $('#total').html(result.data.total);
                $('#impuesto').html(result.data.impuesto);
                $('#addToSale').attr('disabled',true);
                $('#descuento').val('');
                showSnackbar(result.data.message);
            } else {
                alert(result.data.message);
            }
        }
    });
}