(function ($) {

"use strict";


var $document = $(document),
	$window = $(window),
	forms = {
	contactForm: $('#contactForm'),
	questionForm: $('#questionForm'),
	bookingForm: $('#bookingForm'),
	requestForm: $('#requestForm'),
    loginForm: $('#loginForm')
};

    const languages = {
            'sr': {
                'globalErrorMsg': "Nešto nije uredu. Pokušajte ponovo ili kontaktirajte nas.",
                'nameRequired': "Unesite Vaše ime",
                'nameMinLength': "Vaše ima mora da sadrži najmanje 2 slova",
                'messageRequired': "Unesite poruku",
                'messageMinLength': "Vaša poruka mora da sadrži najmanje 20 slova",
                'emailRequired': "Unesite Vašu e adresu",
                'phoneRequired': "Unesite Vaš telefon",
                'phoneRegex': "Vaš telefon nije dobrog formata -  ( xxx/xxx-xxx ) | ( xxx/xxx-xxxx )",
                'bookingNameRequired': "Unesite Vaše ime",
                'bookingNameMinLength': "Vaše ima mora da sadrži najmanje 2 slova",
                'bookingLastNameRequired': "Unesite Vaše prezime",
                'bookingLastNameMinLength': "Vaše prezime mora da sadrži najmanje 2 slova",
                'bookingMessageRequired': "Unesite poruku",
                'bookingMessageMinLength': "Vaša poruka mora da sadrži najmanje 20 karaktera",
                'bookingEmailRequired': "Unesite Vašu e adresu",
            'bookingPhoneRequired': "Unesite Vaš broj telefona",
            'bookingPhoneRegex': "Vaš telefon nije u dobrom formatu.",
            'bookingAgeRequired': "Unesite Vaše godine.",
            'bookingAgeMax': "Godine koje ste uneli nisu validne",
            'bookingDateRequired': "Odaberite datum",
            'bookingTimeRequired': "Odaberite vreme",
            'looginUsernameRequired': 'Unesite korisničko',
            'looginPasswordRequired': 'Unesite lozinku'
        },
        'en': {
        'globalErrorMsg'
    :
        "Something gose wrong.Try again or contact our services.",
            'nameRequired'
    :
        "Please enter your name",
            'nameMinLength'
    :
        "Your name must consist of at least 2 characters",
            'messageRequired'
    :
        "Please enter message",
            'messageMinLength'
    :
        "Your message must consist of at least 20 characters",
            'emailRequired'
    :
        "Please enter your email",
            'phoneRequired'
    :
        "Please enter phone",
            'phoneRegex'
    :
        "Your phone is not in valid format ( xxx/xxx-xxx ) | ( xxx/xxx-xxxx )",
            'bookingNameRequired'
    :
        "Please enter your name",
            'bookingNameMinLength'
    :
        "Name must consist of at least 2 characters",
            'bookingLastNameRequired'
    :
        "Please enter your last name",
            'bookingLastNameMinLength'
    :
        "Last name must consist of at least 2 characters",
            'bookingMessageRequired'
    :
        "Please enter message",
            'bookingMessageMinLength'
    :
        "Your message must consist of at least 20 characters",
            'bookingEmailRequired'
    :
        "Please enter your email",
            'bookingPhoneRequired'
    :
        "Please enter your phone",
            'bookingPhoneRegex'
    :
        "Phone is not in valid format.",
        'bookingAgeRequired'
    :
        "Please enter your age",
            'bookingAgeMax'
    :
        "Age you entered is not valid",
            'bookingDateRequired'
    :
        "Please choose your date",
            'bookingTimeRequired'
    :
        "Please choose your time",
            'looginUsernameRequired'
    :
        "Please enter your username",
            'looginPasswordRequired'
    :
        "Please enter your password"
    }
}


    const translate = (attribute)=>{
        const href = window.location.href
        let lang = 'sr'
        if(href.includes('http://osmehzapromenu.rs/en')) lang = 'en';
        return languages[lang][attribute]
    }


    const phoneRegex= "^[(]?[0-9]{3}[)]?[-\\s\\/]?[0-9]{3}[-]?[0-9]{3,3}$";

    let language = void(0)
    let enableDays = void(0)
    let enableHours =  void(0)
    let today = new Date()
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();
    let currentDate   =dd + '-'+mm + '-' +yyyy;
    let lang = 'en'
    if(localStorage.getItem('osmehLang')){
        lang = localStorage.getItem('osmehLang')
    }


    function disableDates (enableDays) {
        let disabledDays = []
        for(let i=0;i<10;i++){
            let currentYear = new Date().getFullYear()
            currentYear = +currentYear + i;
            for(let i =1;i<13;i++){
                let month = i;
                for(let j =1; j< daysInMonth(month,currentYear)+1;j++){
                    let date = String(j).padStart(2,'0') +"-"+ String(month).padStart(2, '0')+"-"+ currentYear
                    if(enableDays.indexOf(date) !== -1) continue
                    else {
                        let index = disabledDays.findIndex(x=> x === date)
                        if(index !== -1) continue
                        else {
                        	date = String(month).padStart(2, '0') +"-"+ String(j).padStart(2,'0')+"-"+ currentYear
                        	disabledDays.push(moment(date))
                        }
                    }
                }
            }
        }
        return disabledDays
    }

    function disableHours (enableHours) {
        let disableHours = []
        for(let i=0;i<24;i++){
            let hours = i.toString();
            if(enableHours.indexOf(hours) !== -1) continue
            else{
                let index = disableHours.findIndex(x=> x === hours)
                if(index !== -1) continue
                else disableHours.push(hours)
            }
        }
        return disableHours
    }

    function daysInMonth (month, year) {
        return new Date(year, month, 0).getDate();
    }



    const loadAvailableDates = ()=>{
        $.ajax({
            url : 'app/ajax/getAvailableDates.php',
            type: "POST",
            dataType: "JSON",
            success:function(data)
            {
                enableDays = data
				let dates = disableDates(enableDays)
                $('.datetimepicker').datetimepicker({
                    format: 'DD-MM-YYYY',
					ignoreReadonly: true,
                    minDate: new Date(),
                    disabledDates: dates,
                    widgetPositioning:{
                        horizontal: 'left',
                        vertical: 'bottom'
                    },
                    icons: {
                        time: 'icon icon-clock',
                        date: 'icon icon-calendar2',
                        up: 'icon icon-top',
                        down: 'icon icon-bottom',
                        previous: 'icon icon-left',
                        next: 'icon icon-right',
                        today: 'icon icon-tick',
                        clear: 'icon icon-close',
                        close: 'icon icon-close'
                    }
                });
            }
        })
    }
    $('.datetimepicker').on('dp.hide', function(e){
       let value =  moment(e.date._d).format('DD-MM-YYYY')
        onChangeDatePicker(value)
    })




   const onChangeDatePicker = (value) =>{
        if($('.timepicker').val() !== "") $('.timepicker').data("DateTimePicker").destroy()
        $.ajax({
            url : 'app/ajax/getTimeForDate.php',
            type: "POST",
            dataType: "JSON",
            data: {
                date: value
            },
            success:function(data) {
                enableHours = data
                let hours = disableHours(enableHours);
                setTimeout(function(){
                    $('.timepicker').datetimepicker({
						format: 'H',
						ignoreReadonly: true,
						disabledHours: hours,
						icons: {
							time: 'icon icon-clock',
							up: 'icon icon-top',
							down: 'icon icon-bottom',
							previous: 'icon icon-left',
							next: 'icon icon-right'
						}
                	});
            	},200 )
            }

        })
    }



    function page_loader() {
        $('.loading-area').fadeOut(2000)
    };



$document.on('ready', function () {
    function preventBack(){window.history.forward();}
    setTimeout("preventBack()", 0);
    window.onunload=function(){null};

    page_loader();

	// datepicker
	if ($('.datetimepicker').length) {

        loadAvailableDates();
    }

	// contact page form
	if (forms.contactForm.length) {
		var $contactform = forms.contactForm;
		$contactform.validate({
			rules: {
				name: {
					required: true,
					minlength: 2
				},
				message: {
					required: true,
					minlength: 20
				},
				email: {
					required: true,
					email: true
				},
                phone: {
				    required: true,
                    phoneRegex: phoneRegex
                }
			},
			messages: {
				name: {
					required: translate('nameRequired'),
					minlength: translate('nameMinLength'),
				},
				message: {
					required:  translate('messageRequired'),
					minlength: translate('messageMinLength'),
				},
				email: {
					required: translate('emailRequired'),
				},
                phone: {
				    required: translate('phoneRequired'),
                    phoneRegex: translate('phoneRegex'),
				}
			},
			submitHandler: function submitHandler(form) {
			    const data = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    message: $('#message').val(),
                    "g-recaptcha-response": $('textarea[id="g-recaptcha-response"]').val()
                }
				$(form).ajaxSubmit({
                    url: "app/ajax/send-email.php",
					type: "POST",
					data: data,
                    dataType: "JSON",
                    beforeSend: function() {
                        $('.loading-body').delay(1500).fadeOut(750);
                    },
					success: function success() {
                        if(data.type === "message"){
                            str=data.data;
                            swal({
                                title: "Success",
                                text: str,
                                timer: 1000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "success"
                            });
                            $contactform[0].reset();
                            grecaptcha.reset();
                        }else{
                            str=data.data;
                            swal({
                                title: "Error",
                                text: str,
                                timer: 3000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "error"
                            });
                            $contactform.valid()
                        }
					},
					error: function error() {
                        swal({
                            title: "Error",
                            text: translate('globalErrorMsg'),
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "error"
                        });
					}
				});
			}
		});
        $.validator.methods.phoneRegex = function( value, element ) {
            return this.optional( element ) || /^[(]?[0-9]{3}[)]?[-\s\/]?[0-9]{3}[-]?[0-9]{3,4}$/.test( value );
        }
	}



	// booking form
	if (forms.bookingForm.length) {
		var $bookingForm = forms.bookingForm;
		$bookingForm.validate({
			rules: {
				bookingname: {
					required: true,
					minlength: 2
				},
                bookinglastname:{
					required: true,
					minlength: 2
				},
				bookingmessages: {
					required: true,
					minlength: 20
				},
				bookingemail: {
					required: true,
					email: true
				},
                bookingphone: {
                    required: true,
                    phoneRegex: "^[(]?[0-9]{3}[)]?[-\\s\\/]?[0-9]{3}[-]?[0-9]{3,3}$"
                },
                bookingage: {
                    required: true,
                    max: 99
                },
				bookingdate: {
					required:true
				},
				bookingtime: {
					required:true
				}
			},
			messages: {
				bookingname: {
					required: translate('bookingNameRequired'),
					minlength: translate('bookingNameMinLength'),
				},
                bookinglastname: {
                    required: translate('bookingLastNameRequired'),
                    minlength: translate('bookingLastNameMinLength'),
                },
				bookingmessage: {
					required: translate('bookingMessageRequired'),
					minlength: translate('bookingMessageMinLength'),
				},
				bookingemail: {
					required: translate('bookingEmailRequired'),
				},
                bookingphone: {
                    required: translate('bookingPhoneRequired'),
                    phoneRegex: translate('bookingPhoneRegex'),
                },
                bookingage: {
                    required: translate('bookingAgeRequired'),
                    max: translate('bookingAgeMax'),
                },
				bookingdate: {
                    required: translate('bookingDateRequired'),
				},
                bookingtime: {
                    required: translate('bookingTimeRequired'),
				}
			},
		})

        $.validator.methods.phoneRegex = function( value, element ) {
            return this.optional( element ) || /^[(]?[0-9]{3}[)]?[-\s\/]?[0-9]{3}[-]?[0-9]{3,4}$/.test( value );
        }
	}


	/*$('#bookingAppointment').on('click',function(e){
	    let href =$(this).attr('href')
        window.location.replace(href)
    })*/



    $(document).on('submit','#bookingForm',function(e){
        e.preventDefault()
        $('.loading-area').css('display','block');
        $('.loading-area').fadeOut(4000);
		if($bookingForm.valid()){
        	const data = {
        		bookingname: $('#bookingname').val(),
                bookinglastname: $('#bookinglastname').val(),
                bookingemail: $('#bookingemail').val(),
                bookingphone: $('#bookingphone').val(),
                bookingage: $('#bookingage').val(),
                bookingdate: $('#bookingdate').val(),
                bookingtime: $('#bookingtime').val(),
                bookingmessage: $('#bookingmessage').val()
			}
        	$.ajax({
                url: "app/ajax/reservation.php",
                type: "POST",
                data: data,
				dataType: "JSON",
                success: function success(data) {
                    let obj = data
                    let type = obj.type;
                    let str= ""
                    if(type === "ERROR") {
                        str=obj.data;
                        swal({
                            title: "Error",
                            text: str,
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "error"
                        });
                        $bookingForm.valid()
                    }
                    if(type === "OK"){
                        str=obj.data;
                        swal({
                            title: "Success",
                            text: str,
                            timer: 1000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "success"
                        });
                        $bookingForm[0].reset();
                        $('#modalBookingForm').modal('hide');
                        location.reload();
                    }
                },
                error: function error() {
                    swal({
                        title: "Warning",
                        text: translate('globalErrorMsg'),
                        timer: 3000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        type: "error"
                    });
                    $bookingForm.valid()
                }
			})
		}
    })




    if (forms.loginForm.length) {
        var $loginForm = forms.loginForm;
        $loginForm.validate({
            rules: {
                loginusername: {
                    required: true
                },
                loginpassword: {
                    required: true
                }

            },
            messages: {
                loginusername: {
                    required: translate('looginUsernameRequired')
                },
                loginpassword: {
                    required: translate('looginPasswordRequired')
                }
            },
            submitHandler: function submitHandler(form) {
                const data = {
                    loginusername : $('#loginusername').val(),
                    loginpassword: $('#loginpassword').val()
                }
                $(form).ajaxSubmit({
                    type: "POST",
                    data: data,
                    url: "app/ajax/login.php",
                    dataType: "JSON",
                    beforeSend: function() {
                        $('.loading-body').delay(1000).fadeOut(500);
                    },
                    success: function success(data) {
                        console.log(data)
                        let type = data.type
                        if(type === "ERROR") {
                            str=obj.data;
                            swal({
                                title: "ERROR",
                                text: str,
                                timer: 3000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                type: "error"
                            });
                        }

                        if(type === "OK"){
                            localStorage.setItem('token', data.jwt)
                            window.location.replace('admin/index.php')
                        }


                    },
                    error: function error() {
                        swal({
                            title: "Warning",
                            text: translate('globalErrorMsg'),
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            type: "error"
                        });
                        $loginForm.valid()
                    }
                });
            }
        });
    }

});

})(jQuery);