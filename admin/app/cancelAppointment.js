

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








$(document).on('ready', function () {


    const result = requestServer({
        url: "app/ajax/get_cancel_terms.php",
        method: 'POST',
        data: {}
    });


    let dataTable =  $('#cancel_appointment').DataTable({
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
            "info":           "Prikaz _START_ - _END_ od _TOTAL_ termina",
            "infoEmpty":      "Prikaz 0 - 0 od 0 termina",
            "infoFiltered":   "(prikaz _MAX_ termina)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Prikaz _MENU_ termina",
            "loadingRecords": "Učitavanje...",
            "processing":     "Obrada...",
            "search":         "Pretraži:",
            "zeroRecords":    "Nisu pronađeni odgovarajući termini",
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

    $('#cancel_appointment tbody').on( 'click','.preview', function () {
        let id = $(this).attr("id");

        $('#showCancelAppData').modal('show');

        const result = requestServer({
            url: "app/ajax/getDataForTerm.php",
            method: 'POST',
            data: {
                bookingId: id
            }
        });

        $.ajax({
            ...result,
            beforeSend: function() {
                $('.loading-body').delay(1000).fadeOut(500);
            },
            success:function (data) {

                let objPrs = JSON.parse(data);
                let index = objPrs.findIndex(x=>x.id === id);
                let obj = objPrs[index];
                $('#dateTime').text(obj.date+" / "+obj.time);
                $('#firstLastName').text(obj.firstLastName);
                $('#emailTd').text(obj.email);
                $('#phoneTd').text(obj.phone);
                $('#myComment').text(obj.comment);
                $('#emailBody').text(obj.myComment);
                $('#bookingComment').text(obj.emailBody);

            }
        })
    });


})
