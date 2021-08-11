const $productForm = $('#reservationForm')
    let validator = void(0)

    if ($productForm.length) {
        validator = $productForm.validate({
            rules: {
                name: {
                    required: true,
                },
                phone: {
                    required: true,
                },
                date: {
                    required: true,
                },
                service: {
                    required: true,
                }

            },
            messages: {
                name: {
                    required: 'Unesite ime i prezime',
                },
                phone: {
                    required: 'Unesite broj telefona',
                },
                date: {
                    required: 'Odaberite datum i vreme',
                },
                service: {
                    required: 'Odaberite uslugu',
                }
            },
            submitHandler: function submitHandler(form) {
                event.preventDefault();
                $.ajax({
                    url: "php_vendors/.php",
                    method: 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    cache: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    success: function(data) {
                        let objResp = JSON.parse(data);
                        let str = objResp.type;

                        if (str === 'ERROR') {
                            str = objResp.data;
                            swal({
                                title: "Error",
                                text: str,
                                timer: 3000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "error"
                            });
                            //$('#product_form')[0].reset();
                            return;
                        }

                        if (str === 'OK') {
                            str = objResp.data;
                            swal({
                                title: "Success",
                                text: str,
                                timer: 1000,
                                showCancelButton: false,
                                showConfirmButton: true,
                                type: "success"
                            });
                            //  $('#reservationForm')[0].reset();
                            //$('#exampleModalCenter').modal('hide');
                            //dataTable.ajax.reload();
                        }

                    }
                })
            }
        })
    }