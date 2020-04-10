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
            }
        });
    });
    $('#searchProduct').click(function(e){
        var searchProduct = $('#product').val();
        if (searchProduct !== '') {
            $('#name-data-material').html('');
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: urlFindProduct,
                method: 'post',
                data: {
                    product: searchProduct,
                    limit: 10,
                },
                success: function(result){
                    $('#product-search-result').html(result.html);
                }
            });
        }
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
            }
        });
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
                }
            });
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
                }
            });
        }
    });
    // Finish sale
    $('#finish').click(function(e){
        var aditional_detail = $('#aditional_detail').val();
        var quote_date = $('#quote_date').val();
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
                aditional_detail: aditional_detail,
                quote_date: quote_date,
            },
            success: function(result){
                if (result.success) {
                    window.location.href = '/quote/invoices';
                } else {
                    showSnackbarError(result.data.message);
                }
            }
        });
    });
    // Add product to sale
    $('#addToSale').click(function(e){
        $('#spinner').show();
        var quoteId = $('#finish').val();
        var locationId = $('#locationId').val();
        var aditional_detail = $('#aditional_detail').val();
        var quote_date = $('#quote_date').val();
        var categoryDescription = $('#categoryDescription').val();
        var place = $('#place').val();
        var category = $('#category').val();

        var price = $('#price').val();
        var quantity = $('#quantity').val();
        var optional = document.getElementById('optional').checked;
        var offer = document.getElementById('offer').checked;
        var package = document.getElementById('package').checked;
        var other = $('#other').val();
        var description = $('#description').val();

        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlAddProduct,
            method: 'post',
            data: {
                quoteId: quoteId,
                id: $(this).val(),
                locationId: locationId,
                aditional_detail: aditional_detail,
                quantity: quantity,
                price: price,
                other: other,
                description: description,
                categoryDescription: categoryDescription,
                quote_date: quote_date,
                place: place,
                category: category,
                optional: optional,
                offer: offer,
                package: package,
            },
            success: function(result){
                $('#spinner').hide();
                if (result.success) {
                    $('#productDetailBody').html(result.data.html);
                    // $('#totalOthers').html(result.data.descuentos);
                    // $('#subTotal').html(result.data.subTotal);
                    // $('#total').html(result.data.total);
                    // $('#tax').html(result.data.tax);
                    $('#addToSale').attr('disabled',true);
                    $('#cancelItem').attr('disabled',true);
                    // $('#package').attr('disabled',false);
                    $('#price').val('');
                    $('#other').val('');
                    $('#description').val('');
                    $('#finish').val(result.data.quoteId);
                    // $('#finish').show();
                    var y = document.getElementsByClassName("devis-stared");
                    for (var i = 0; i < y.length; i++) {
                        y[i].style.display = "block";
                    }
                    // $('#finish').show(result.data.html);
                    $('#divAddProduct').hide();
                    showSnackbar(result.data.message);
                } else {
                    showSnackbarError(result.data.message);
                }
            }
        });
    });
    $('#cancelItem').click(function(e){
        $('#addToSale').attr('disabled',true);
        $('#cancelItem').attr('disabled',true);
        // $('#package').attr('disabled',false);
        $('#price').val('');
        $('#other').val('');
        $('#description').val('');
        $('#divAddProduct').hide();
    });
    $('#package').click(function(e){
        var product = $('#addToSale').val();
        var package = e.checked;
        if ($(this).is(':checked')) {
            $('.product').hide();
            $('.price').val('');
        } else if (product) {
            $('.product').show();
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: urlChoiceProduct,
                method: 'post',
                data: {
                    id: product,
                    locationId: $('#locationId').val(),
                },
                success: function(result){
                    if (result.success) {
                        $('#price').val(result.data.info['price']);
                        $('#other').attr('disabled',false);
                    } else {
                        showSnackbarError(result.data.message);
                    }
                }
            });
        }
    });
    $('.optionalOffer').click(function(e){
        var optional = document.getElementById('optional').checked;
        var offer = document.getElementById('offer').checked;
        if (optional === true || offer === true) {
            $('#other').attr('disabled',true);
        } else {
            $('#other').attr('disabled',false);
        }
    });
    $('#updateQuoteDetail').click(function(e){
        $('#spinner').show();
        var itemCategory = $('#itemCategory').val();
        var productId = $('#addToSale').val();
        var quantity = $('#quantity').val();
        var price = $('#price').val();
        var other = $('#other').val();
        var description = $('#description').val();
        var categoryDescription = $('#categoryDescription').val();
        var category = $('#category').val();
        var place = $('#place').val();
        var optional = document.getElementById('optional').checked;
        var offer = document.getElementById('offer').checked;
        var package = document.getElementById('package').checked;
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlUpdateQuoteDetail,
            method: 'post',
            data: {
                id: $(this).val(),
                productId: productId,
                quantity: quantity,
                price: price,
                other: other,
                description: description,
                categoryDescription: categoryDescription,
                category: category,
                place: place,
                optional: optional,
                offer: offer,
                itemCategory: itemCategory,
                package: package,
            },
            success: function(result){
                $('#spinner').hide();
                if (result.success) {
                    $('#productDetailBody').html(result.data.html);
                    // $('#totalOthers').html(result.data.descuentos);
                    // $('#subTotal').html(result.data.subTotal);
                    // $('#total').html(result.data.total);
                    // $('#tax').html(result.data.tax);
                    $('#addToSale').attr('disabled',true);
                    $('#cancelItem').attr('disabled',true);
                    // $('#package').attr('disabled',false);
                    $('#price').val('');
                    document.getElementById('package').checked = false;
                    $('#other').val('');
                    $('#description').val('');
                    $('#divAddProduct').hide();
                    showSnackbar(result.data.message);
                } else {
                    showSnackbarError(result.data.message);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#spinner').hide();
                var msg = thrownError;
                showSnackbarError(msg);
            }
        });
    });
    // $('.itemName').select2({
    //     placeholder: 'Select an item',
    //     theme: 'bootstrap4',
    //     ajax: {
    //         url: urlautocompleteAjax,
    //         dataType: 'json',
    //         delay: 250,
    //         processResults: function (data) {
    //             return {
    //                 results: $.map(data, function (item) {
    //                     return {
    //                         text: item.name,
    //                         id: item.id
    //                     }
    //                 })
    //             };
    //         },
    //         cache: true
    //     }
    // });
    $('#uploadDevisFiles').on('submit', function(event){
        $('#spinner').show();
        event.preventDefault();
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var quoteId = $('#finish').val();
        var formData = new FormData(this);
        formData.append("id", quoteId);
        $.ajax({
            url: urlUploadQuoteFiles,
            method:"POST",
            data:formData,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success:function(result){
                $('#spinner').hide();
                $('#uploaded_image').html(result.data.html);
                if (result.success) {
                    showSnackbar(result.data.message);
                } else {
                    showSnackbarError(result.data.message);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#spinner').hide();
                var msg = thrownError;
                if (xhr.responseJSON.errors.quoteFiles) {
                    msg = xhr.responseJSON.errors.quoteFiles[0];
                } else if (xhr.responseJSON.errors['quoteFiles.0']) {
                    msg = xhr.responseJSON.errors['quoteFiles.0'][0];
                }
                showSnackbarError(msg);
            }
        })
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
            }
        });
    }
}
function choiceAccount(e) {
    $('#locationId').val(e.value);
    $('#divAgrVenta').hide();
    $('#divSelectProduct').show();
    $('#addToSale').attr('disabled',true);
    $('#cancelItem').attr('disabled',true);
    $('#divSaleDetail').show();
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
            locationId: $('#locationId').val(),
        },
        success: function(result){
            if (result.success) {
                $('#addToSale').val(e.value);
                $('#productName').val(result.data.info['name']);
                $('#code').val(result.data.info['code']);
                $('#price').val(result.data.info['price']);
                $('#addToSale').attr('disabled',false);
                $('#cancelItem').attr('disabled',false);
                // $('#package').attr('disabled',true);
                $('#updateQuoteDetail').hide();
                $('#product').val('');
                $('#name-data-material').html('');
                $('#divAddProduct').show();

                // var x = document.getElementById("snackbarError");
                // x.className = "product";

                var package = document.getElementById('package').checked;
                if (package === true) {

                    // setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);

                    $('.product').hide();
                }

            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}
// function getSecondCategory(e) {
//     var quoteId = $('#finish').val();
//     $.ajaxSetup({
//         headers: {
//           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         }
//     });
//     $.ajax({
//         url: urlGetSecondCategory,
//         method: 'post',
//         data: {
//             id: e.value,
//             quoteId: quoteId,
//         },
//         success: function(result){
//             $('#place').html(result.data.html);
//             if (result.data.info !== null) {
//                 $('#categoryDescription').val(result.data.info['descripcion']);
//             } else {
//                 $('#categoryDescription').val('');
//             }
//         }
//     });
// }

function savePackageValue(e) {
    $('#spinner').show();
    var packageValue = $('#packageValue-'+e.value).val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlSavePackageValue,
        method: 'post',
        data: {
            id: e.value,
            packageValue: packageValue,
        },
        success: function(result){
            $('#spinner').hide();
            if (result.success) {
                $('#totales').html(result.data.html);
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#spinner').hide();
            showSnackbarError(thrownError);
        }
    });
}
// Remove detail
function removeItem(e) {
    $('#spinner').show();
    var quoteId = $('#finish').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlRemoveItem,
        method: 'post',
        data: {
            id: e.value,
            quoteId: quoteId,
        },
        success: function(result){
            $('#spinner').hide();
            if (result.success) {
                $('#productDetailBody').html(result.data.html);
                $('#totalOthers').html(result.data.descuentos);
                $('#subTotal').html(result.data.HT);
                $('#total').html(result.data.total);
                $('#tax').html(result.data.tax);
                $('#divAddProduct').hide();
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#spinner').hide();
            showSnackbarError(thrownError);
        }
    });
}
// Edit detail
function editItem(e) {
    $('#spinner').show();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlEditQuoteDetail,
        method: 'post',
        data: {
            id: e.value,
        },
        success: function(result){
            $('#spinner').hide();
            if (result.success) {
                $('#updateQuoteDetail').show();
                $('#updateQuoteDetail').val(e.value);
                $('#addToSale').val(result.data.info['product_id']);
                $('#itemCategory').val(result.data.info['itemCategory']);

                $('#categoryDescription').val(result.data.info['categoryDescription']);

                $('#productName').val(result.data.info['name']);
                $('#code').val(result.data.info['code']);
                $('#price').val(result.data.info['price']);
                $('#quantity').val(result.data.info['quantity']);
                $('#other').val(result.data.info['other']);
                $('#description').val(result.data.info['description']);
                document.getElementById('category').value = result.data.info['category'];
                document.getElementById('place').value = result.data.info['place'];
                document.getElementById('offer').checked = false;
                document.getElementById('optional').checked = false;
                document.getElementById('package').checked = false;
                $('#divAddProduct').show();
                $('.product').show();
                $('#cancelItem').attr('disabled',false);
                $('#other').attr('disabled',false);
                if (result.data.info['offer'] === 1 || result.data.info['optional'] === 1 || result.data.info['package'] === 1) {
                    $('#other').attr('disabled',true);
                }
                if (result.data.info['offer'] === 1) {
                    document.getElementById('offer').checked = true;
                }
                if (result.data.info['optional'] === 1) {
                    document.getElementById('optional').checked = true;
                }
                if (result.data.info['package'] == 1) {
                    document.getElementById('package').checked = true;
                    $('.product').hide();
                }
            } else {
                showSnackbarError(result.data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#spinner').hide();
            showSnackbarError(thrownError);
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
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);
}
// end style
function initPage() {
    $('#spinner').show();
    var id = $('#cancel').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlLoadQuoteDetail,
        method: 'post',
        data: {
            id: id,
        },
        success: function(result){
            $('#spinner').hide();
            if (result.success) {
                $('#productDetailBody').html(result.data.html);
                $('#totalOthers').html(result.data.descuentos);
                $('#subTotal').html(result.data.HT);
                $('#total').html(result.data.total);
                $('#tax').html(result.data.tax);
                $('#addToSale').attr('disabled',true);
                $('#cancelItem').attr('disabled',true);
                $('#other').val('');
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#spinner').hide();
            showSnackbarError(thrownError);
        }
    });
}



function initFactura() {
    $('#spinner').show();
    var id = $('#cancel').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlLoadQuoteDetail2,
        method: 'post',
        data: {
            id: id,
        },
        success: function(result){
            $('#spinner').hide();
            if (result.success) {
                $('#productDetailBody').html(result.data.html);
                $('#totalOthers').html(result.data.descuentos);
                $('#subTotal').html(result.data.HT);
                $('#total').html(result.data.total);
                $('#tax').html(result.data.tax);
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            showSnackbarError(thrownError);
            $('#spinner').hide();
        }
    });
}
function approveItem(e) {
    $('#spinner').show();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlApproveItem,
        method: 'post',
        data: {
            id: e.value,
            invoice: e.checked,
        },
        success: function (result) {
            $('#spinner').hide();
            showSnackbar(result.data.message);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#spinner').hide();
            showSnackbarError();
        }
    });
}
function checkAll(e) {
    var id = e.value;
    checkboxes = document.getElementsByName('item'+id);
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = e.checked;
    }
}
// Finish sale
function finishFacturaa(e) {
    var invoice_date = $('#invoice_date').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlFinishFactura,
        method: 'post',
        data: {
            id: e.value,
            invoice_date: invoice_date,
        },
        success: function(result){
            if (result.success) {
                window.location.href = '/quote/invoices';
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}

function finishReactivar(e) {
    // var quote_date = $('#quote_date').val();
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlFinishReactivar,
        method: 'post',
        data: {
            id: e.value,
            // quote_date: quote_date,
        },
        success: function(result){
            if (result.success) {
                window.location.href = '/quote/quotes';
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}
function removeFile(id) {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: urlRemoveFile,
        method: 'post',
        data: {
            id: id,
        },
        success: function(result){
            if (result.success) {
                $('#uploaded_image').html(result.data.html);
                showSnackbar(result.data.message);
            } else {
                showSnackbarError(result.data.message);
            }
        }
    });
}