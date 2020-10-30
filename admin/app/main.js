const $setTermsForms = $("#setTermsByDateForm")

const requestServer = (object) =>{
    let jwt = localStorage.getItem('token')
    object.data = {
        ...object.data,
        ...{
            jwt: jwt
        }
    }
   return object
}




var dataTable = void (0);

$(document).on('ready', function () {



    if ($setTermsForms.length) {
        $setTermsForms.validate({
            rules: {
                term_date: {
                    required: true,
                    minDate: true,
                },
                start_time:{
                    required: true,
                    min: 8,
                    max: 17
                },
                end_time: {
                    required: true,
                    min: 8,
                    max: 17
                }
            },
            messages: {
                bookingname: {
                    required: "Please choose date",
                    maxDate: "Invalid date!"
                },
                bookinglastname: {
                    required: "Please choose start time",
                    min: "Min 8 h",
                    max: "Max 17 h"
                },
                bookingmessage: {
                    required: "Please choose end time",
                    min: "Min 8 h",
                    max: "Max 17 h"
                }
            },
        })

        $.validator.addMethod("minDate", function(value, element) {
            var curDate = new Date();
            var inputDate = new Date(value);
            if (inputDate > curDate)
                return false;
            return true;
        });

    }

    const result = requestServer({
        url: "app/ajax/get_terms.php",
        method: 'POST',
        data: {}
    });

   dataTable =  $('#terms_dates').DataTable({
        "processing" : true,
        "serverSide":true,
        "responsive": true,
        "Paginate": true,
        "pager":{
           container:"paging_here", // the container to place the pager controls into
           lenght:10, // the number of records per a page
           start:0   // the number of pages in the pager
        },
        "order":[[1,"asc"]],
        "ajax": result,
        "columnDefs": [ {
            "targets": -1,
        } ],
        "language": {
            "decimal":        "",
            "emptyTable":     "Nema podataka u tabeli",
            "info":           "Prikaz _START_ - _END_ od _TOTAL_ datuma",
            "infoEmpty":      "Prikaz 0 - 0 od 0 datuma",
            "infoFiltered":   "(prikaz _MAX_ datuma)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Prikaz _MENU_ datuma",
            "loadingRecords": "Učitavanje...",
            "processing":     "Obrada...",
            "search":         "Pretraži:",
            "zeroRecords":    "Nisu pronađeni odgovarajući datumi",
            "paginate": {
                "first":      "Prvi",
                "last":       "Poslednji",
                "next":       "Sledeći",
                "previous":   "Prethodni"
            },
            "aria": {
                "sortAscending":  ": Sortiranje uzlazno",
                "sortDescending": ": Sortiranje silazno"
            }
        }
    });

    $setTermsForms.on('submit',function(e){
        e.preventDefault()
        if($setTermsForms.valid()){
            const insertData = requestServer({
                url: "app/ajax/set_new_term.php",
                method: 'POST',
                dataType: "JSON",
                cache: false,
                data: {
                    termDate: $('#term_date').val(),
                    startTime: $('#start_time').val(),
                    endTime: $('#end_time').val()
                }
            });

            setTimeout(()=>{
                $.ajax({
                    ...insertData,
                    success:function (data) {
                        console.log(data)
                        let type = data.type
                        if(type === "ERROR"){
                            str=data.data;
                            swal({
                                title: "Error",
                                text: str,
                                timer: 3000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "error"
                            });
                            $setTermsForms.valid()
                            return;
                        }

                        if(type === "OK") {
                            str=data.data;
                            swal({
                                title: "Success",
                                text: str,
                                timer: 1000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "success"
                            });
                            $setTermsForms[0].reset();
                            dataTable.ajax.reload();
                        }
                    },
                    error: function error() {

                    }
                 })
            },400)

        }
    })

})

/*

$('#datetimepicker1').on('changeDate',function(e){
        const result = requestServer({
            url: "app/ajax/getTime.php",
            method: 'POST',
            dataType: "JSON",
            cache: false,
            data: {}
        });

        $.ajax({
            ...result,
            success: function (data) {
            console.log(data)
            } ,
            error: function error() {

            }
        })

});


*/
