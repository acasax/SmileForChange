

    let eventsFullList = []
    let eventsList = []
    var calendar = void(0)
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


    const getEventList = ()=>{
        const requestAjax = requestServer({
            url: "app/ajax/getAllTerms.php",
            method: "POST",
            dataType: "JSON"})
        $.ajax({
            ...requestAjax,
            ...{
              success:function (data) {
                  eventsFullList = data;
                  $.each(data,function(elem){
                      eventsList.push(data[elem]['eventList']);
                  })

                  setTimeout(()=>{
                      var calendarEl = document.getElementById('calendar');
                      calendar = new FullCalendar.Calendar(calendarEl, {
                          plugins: [ 'dayGrid', 'timeGrid', 'list', 'interaction' ],
                          header: {
                              left: 'prev,next today',
                              center: 'title',
                              right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                          },
                          defaultDate: new Date(),
                          weekends: false,
                          navLinks: true, // can click day/week names to navigate views
                          editable: false,
                          eventLimit: true, // allow "more" link when too many events
                          events: eventsList,
                          select: function(start, end) {
                              // Display the modal.
                              // You could fill in the start and end fields based on the parameters
                              $('.modal').modal('show');

                          },
                          eventClick: function(ev, element) {
                              // Display the modal and set the values to the event values.
                              $('.modal').modal('show');
                              const event = ev.event
                              let start = new Date(event.start)
                              start = start.getFullYear()+ "-"+(start.getMonth()+1)+ "-"+ String(start.getDate()).padStart(2, '0') + " "+ String(start.getHours()).padStart(2, '0') + ":00";
                              let object = eventsFullList.find(x=> x.eventList.start === start);
                              let status = 'Not reserved';
                              let activeClass = 'notReserved';
                              switch(object.bookingStatus){
                                  case 'S': activeClass='standBy'; status = 'Stand By'; break;
                                  case 'R': status = 'Reserved'; activeClass='reserved'; break;
                              }
                              let $status = $('.modal').find('.eventStatusDiv');

                              if($status.hasClass('notReserved')) $status.removeClass('notReserved')
                              if($status.hasClass('reserved')) $status.removeClass('reserved')
                              if($status.hasClass('stasndBy')) $status.removeClass('standBy')
                              $('.modal').find('#modalTitle').text(event.title);
                              $status.text(status).addClass(activeClass);
                              $('.modal').find('#firstLastName').text(object.bookingFirstName + " " + object.bookingLastName);
                              $('.modal').find('#email').text(object.bookingEmail);
                              $('.modal').find('#phone').text(object.bookingPhone);
                              $('.modal').find('#comment').text(object.bookingComment);
                              $('.modal').find('#bookingId').val(object.bookingId);


                              if(object.bookingStatus === 'S'){
                                  $('#save-event').addClass('show');
                                  $('#cancel-event').addClass('show');
                              }else{
                                  if($('#save-event').hasClass('show') &&  $('#cancel-event').hasClass('show')){
                                      $('#save-event').removeClass('show');
                                      $('#cancel-event').removeClass('show');
                                  }
                              }
                          },
                      });
                    calendar.render();
                  },500)
              }
            }
        })

    }

    getEventList();


    const acceptOrRejectTerm = (bookingId,status) => {
        const updateData = requestServer({
            url: "app/ajax/acceptTerm.php",
            method: "POST",
            dataType: "JSON",
            data: {
                bookingId: bookingId,
                bookingStatus : status,
                myComment: $('#myComment').val(),
                mailMessage: $('#mailMessage').val()
            }
        })

        $.ajax({
            ...updateData,
            ...{
                beforeSend: function() {
                    $('.loading-body').delay(1000).fadeOut(500);
                },
                success:function (data) {
                    let type = data.type
                    if (type === "ERROR") {
                        str = data.data;
                        swal({
                            title: "Error",
                            text: str,
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "error"
                        });
                        return;
                    }
                    if (type === "OK") {
                        str = data.data;
                        swal({
                            title: "Success",
                            text: str,
                            timer: 1000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "success"
                        });
                        location.reload();
                        $('.modal').modal('hide');
                    }
                }
            }
        })
    }

    // Bind the dates to datetimepicker.
    // You should pass the options you need
   // $("#starts-at, #ends-at").datetimepicker();

    // Whenever the user clicks on the "save" button om the dialog
    $('#save-event').on('click', function() {
        const bookingId = $('#bookingId').val();
        const bookingStatus = 'R';
        acceptOrRejectTerm(bookingId,bookingStatus);
        // hide modal

    });

    $('#cancel-event').on('click',function(){
        const bookingId = $('#bookingId').val();
        const bookingStatus = 'C';
        acceptOrRejectTerm(bookingId,bookingStatus);

    })