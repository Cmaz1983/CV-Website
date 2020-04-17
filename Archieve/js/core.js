// Track if the menus are open
var navOpen = false; // Mobile navigation menu

// Track if we are the top 100px of the page
var scrollTop = false;

// Email address checking regex
var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

"use strict";

// Wait for the page to finish loading
$(document).ready(function () {

    // Select all links with hashes
    $('a[href*="#"]')
        // Remove links that don't actually link to anything
        .not('[href="#"]')
        .not('[href="#0"]')
        .click(function (event) {
            // On-page links
            if (
                location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
                &&
                location.hostname == this.hostname
            ) {
                // Figure out element to scroll to
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                // Does a scroll target exist?
                if (target.length) {
                    // Only prevent default if animation is actually gonna happen
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 1000, function () {
                        // Callback after animation
                        // Must change focus!
                        var $target = $(target);
                        $target.focus();
                        if ($target.is(":focus")) { // Checking if the target was focused
                            return false;
                        } else {
                            $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
                            $target.focus(); // Set focus again
                        };
                    });
                }
            }
        });


    //*******************************
    //  Truncate and read more controller for timeline
    //*******************************
    let descriptions = $('.desc-content');

    for (var i = 0; i < descriptions.length; i++) {
        let item = $('.desc-content')[i]
        let desc = item.innerHTML;

        let pos = 700;
        let sym = "...";

        let trunc = "";

        let len = desc.length;
        if (pos == undefined || len <= pos) {
            trunc = desc;
        } else {
            let p1 = desc.slice(0, pos - 3);
            let p2 = desc.slice(pos - 3);
            trunc = `
            <div class="spoiler-${i}">${p1} ${sym}</div>
            <div class="seemore" data="${i}"><span class="moretext"> See more</span></div>
            <div class="desc-more-${i} hidden">${desc}</div>
            `;
        }

        item.innerHTML = trunc;

    }

    $('.seemore').on("click", (target) => {
        let data = $(target)[0].currentTarget.attributes[1].value;
        let spoiler = ".spoiler-" + data;
        let all = ".desc-more-" + data;

        $($(target)[0].currentTarget).hide();
        $(spoiler).hide();
        $(all).show();
    })

    //*******************************
    //  ___END_OF___   Truncate and read more controller for timeline
    //*******************************





    // If the cookie consent button is present
    if ($('#cookie-consent-btn').length > 0) {
        // ..Listen for a click
        $('#cookie-consent-btn').click(function () {
            // ..Set the cookie
            setCookie('cookie-consent', 'true', 100);
            // ..Slide close the notification
            $('#cookie-consent').slideToggle();
        });
    };

    $('.mobile-burger').click(function () {
        if (navOpen) {
            $('.website-container').animate({
                'left': '0'
            })
            $('footer').animate({
                'left': '0'
            })
            $($('.hamburger')[0]).removeClass('is-active');
            navOpen = false;
        } else {
            $('.website-container').animate({
                'left': '-50%'
            })
            $('footer').animate({
                'left': '-50%'
            })
            $($('.hamburger')[0]).addClass('is-active');
            navOpen = true;
        }
    });



}) // End of document load



function submitLogin(form) {
    event.preventDefault();

    var username = $('#login-form')[0][0].value;
    var password = $('#login-form')[0][1].value;

    var loginMsg = $('#login-error')[0];
    var loginError = $('#login-error');

    var succMsg = $('#login-success')[0];
    var loginSucc = $('#login-success');

    if (username.length > 0 && username.length < 40 && password.length > 0 && password.length < 40) {

        $.ajax({
            'url': '/mng/user.mng.php?action=login',
            'type': 'POST',
            'data': {
                'username': username,
                'password': password
            },
            success: function (response) {
                var data = JSON.parse(response);

                if (data.code == 200) {
                    succMsg.innerHTML = "Sucess! Redirecting now...";
                    loginError.hide();
                    loginSucc.show();
                    location.reload();
                } else {
                    loginMsg.innerHTML = data.message;
                    loginError.show();
                }
            },
            error: function () {
                loginMsg.innerHTML = "We had an error logging you in, please try again later.";
                loginError.show();
            }
        })

    } else {
        loginMsg.innerHTML = "Please enter a username and password.";
        loginError.show();
    }
};

function logout() {
    $.ajax({
        'url': '/mng/user.mng.php?action=logout',
        success: function () {
            window.location = '/';
        },
        error: function () {
            //TODO error logout.
        }
    })
};

function contactHandler() {

    event.preventDefault();

    var form = $('#contact-form')[0];

    var error = false;
    var errorMessage = $('#contact-error');
    var succMessage = $('#contact-message');
    succMessage.hide();
    errorMessage.hide();

    var contact = {
        'name': form[0].value,
        'subject': form[1].value,
        'email': form[2].value,
        'message': form[3].value
    };

    if (!contact.name.length > 0) {
        $(form[0]).addClass('is-invalid');
        error = true;
    } else {
        $(form[0]).removeClass('is-invalid');
    };
    if (!contact.subject.length > 0) {
        $(form[1]).addClass('is-invalid');
        error = true;
    } else {
        $(form[1]).removeClass('is-invalid');
    };
    if (!contact.email.length > 0) {
        $(form[2]).addClass('is-invalid');
        error = true;
    } else {
        if (re.test(String(contact.email).toLowerCase())) {
            $(form[2]).removeClass('is-invalid');
        } else {
            $(form[2]).addClass('is-invalid');
            error = true;
        };

    };
    if (!contact.message.length > 0) {
        $(form[3]).addClass('is-invalid');
        error = true;
    } else {
        $(form[3]).removeClass('is-invalid');
    };


    if (error) {
        errorMessage[0].innerHTML = "Please enter valid information.";
        errorMessage.show();
    } else {
        errorMessage.hide();
        $.ajax({
            'url': '/mng/contactMessage.mng.php',
            'data': contact,
            'timeout': 6000,
            'type': 'POST',
            success: function (data) {
                var response = JSON.parse(data);
                if (response.code == 200) {
                    errorMessage.hide();
                    for (var i = 0; i < form.length; i++) {
                        form[i].value = "";
                    };
                    succMessage[0].innerHTML = "Message sent.";
                    succMessage.show();
                } else {
                    errorMessage[0].innerHTML = "An error occured, please try again later.";
                    errorMessage.show();
                };
            },
            error: function () {
                errorMessage[0].innerHTML = "An error occured, please try again later.";
                errorMessage.show();
            }
        });
    };
};


function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}

function getUrlParam(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    var c = url.searchParams.get(param);
    return c;
};

function submitRegister() {
    // Prevend the page from submitting
    event.preventDefault();
    // Get the values from the fields.
    var form = $('#register-form')[0];
    var username = form[0];
    var email = form[1];
    var password = form[2];
    var confirm = form[3];
    var button = $(form[4]);
    // Set the error flag.
    var flag = false;
    button[0].innerHTML = "Checking details...";
    button.addClass("disabled");

    var errorContianer = $($('.register-form')[0].children[1]);
    var successContianer = $($('.register-form')[0].children[0]);

    errorContianer.hide();
    successContianer.hide();


    // Check all the fields are filled in. 
    if (username.value.length > 0) {
        $(username).removeClass("is-invalid")
    } else {
        $(username).addClass("is-invalid")
        flag = "Please fill all of the information";
    }
    if (email.value.length > 0) {
        if (re.test(String(email.value).toLocaleLowerCase())) {
            $(email).removeClass("is-invalid");
            $(email).addClass('is-valid');
        } else {
            $(email).addClass("is-invalid")
            flag = "Email invalid";
        }

    } else {
        $(email).addClass("is-invalid")
        flag = "Please fill all of the information";
    }
    if (password.value.length > 0) {
        $(password).removeClass("is-invalid")
    } else {
        $(password).addClass("is-invalid")
        flag = "Please fill all of the information";
    }
    if (confirm.value.length > 0) {
        $(confirm).removeClass("is-invalid")
    } else {
        $(confirm).addClass("is-invalid")
        flag = "Please fill all of the information";
    }



    // Check if the flag was set. 
    if (!flag) {

        // Send the data and handle the ouput.
        if (checkPassword()) {
            if (checkEmail()) {
                // Sending to get checked now
                $.ajax({
                    'url': '/mng/user.mng.php?action=register',
                    'type': 'POST',
                    'timeout': 60000,
                    'data': {
                        'username': username.value,
                        'password': password.value,
                        'email': email.value,
                        'confirm': confirm.value
                    },
                    success: function (data) {
                        var response = JSON.parse(data);
                        if (response.code == 200) {
                            errorContianer.hide();
                            successContianer[0].innerHTML = "User has been registered. You can now login.";
                            successContianer.show();
                        } else {
                            errorContianer[0].innerHTML = response.message;
                            errorContianer.show();
                        };
                        button[0].innerHTML = "Register";
                        button.removeClass("disabled");
                    },
                    error: function () {

                    }
                });
            };
        };

    } else {
        // Update the error
        console.log(flag);
        errorContianer[0].innerHTML = flag;
        errorContianer.show();
        button[0].innerHTML = "Register";
        button.removeClass("disabled");
    }
};

function checkEmail() {

    var form = $('#register-form')[0];
    var email = form[1];
    if (re.test(String(email.value).toLocaleLowerCase())) {
        $(email).removeClass('is-invalid');
        $(email).addClass('is-valid');
        return true;
    } else {
        $(email).removeClass('is-valid');
        $(email).addClass('is-invalid');
        return false;
    }
}

function checkUsername() {
    var form = $('#register-form')[0];
    var username = form[0];
    var errorContianer = $($('.register-form')[0].children[1]);
    var usernameMessages = $($('#register-form')[0][0]).siblings();
    if (username.value.length > 4) {
        $.ajax({
            'url': '/mng/user.mng.php?action=checkUsername',
            'type': 'POST',
            'timeout': 60000,
            'data': {
                'username': username.value
            },
            success: function (data) {
                var response = JSON.parse(data);
                if (response.code == 200) {
                    errorContianer.hide();

                    if (response.message == "true") {
                        $(username).removeClass('is-invalid');
                        $(username).addClass('is-valid');
                    } else {
                        usernameMessages[1].innerHTML = "Username taken.";
                        $(username).removeClass('is-valid');
                        $(username).addClass('is-invalid');
                    }
                } else {

                    errorContianer[0].innerHTML = "An error occured, please try again later.";
                    errorContianer.show();
                    console.log('An error occured');
                }
            }
        });
    } else {
        usernameMessages[1].innerHTML = "Username too short.";
        $(username).removeClass('is-valid');
        $(username).addClass('is-invalid');
    }

}
function checkPassword() {
    var form = $('#register-form')[0];
    var password = form['password'];
    var flag = false;
    var att = {
        digits: $($('.password-requirements')[0].children[0]),
        lower: $($('.password-requirements')[0].children[4]),
        nonWords: $($('.password-requirements')[0].children[1]),
        upper: $($('.password-requirements')[0].children[2])
    }

    var checks = {
        digits: /\d/.test(password.value),
        lower: /[a-z]/.test(password.value),
        upper: /[A-Z]/.test(password.value),
        nonWords: /\W/.test(password.value),
    }

    for (var check in checks) {

        if (checks[check] == true) {
            att[check].css({ 'color': 'green' });
        } else {
            flag = true;
            att[check].css({ 'color': 'red' });
        }

    }
    if (flag) {

        $(password).addClass('is-invalid');
        $(password).removeClass('is-valid');
        return false;

    } else {

        $(password).removeClass('is-invalid');
        $(password).addClass('is-valid');
        return true;

    }
}

function requestPasswordReset() {
    event.preventDefault();

    var form = $('#forgot-password-form')[0];
    var email = form[0];
    var button = form[1];

    var error = $('.forgot-form')[0].children;
    var successMessage = $($('.forgot-form')[0].children[1]);
    var errorMessage = $($('.forgot-form')[0].children[0]);


    $(button).addClass('disabled');


    if (re.test(email.value)) {
        $(email).removeClass('is-invalid');
        $(email).addClass('is-valid');
        $.ajax({
            'url': '/mng/user.mng.php?action=forgot',
            'type': 'POST',
            'timeout': 60000,
            'data': {
                'email': email.value
            },
            success: function (data) {
                var response = JSON.parse(data);
                console.log(response);
                if (response.code == 200) {
                    $(errorMessage).hide();
                    $(successMessage)[0].innerHTML = "Reset email sent";
                    $(successMessage).show();
                } else {
                    $(successMessage).hide();
                    $(errorMessage)[0].innerHTML = response.message;
                    $(errorMessage).show();
                }
            },
            error: function () {
                $(successMessage).hide();
                $(errorMessage)[0].innerHTML = "An error occured, please try again later.";
                $(errorMessage).show();
            }
        })
    } else {
        $(email).removeClass('is-valid');
        $(email).addClass('is-invalid');
        $(button).removeClass('disabled');
    };

}




// Edge smooth scrolling HACK
if (navigator.userAgent.match(/MSIE 10/i) || navigator.userAgent.match(/Trident\/7\./) || navigator.userAgent.match(/Edge\/12\./)) {
    $('body').on("mousewheel", function () {
        event.preventDefault();
        var wd = event.wheelDelta;
        var csp = window.pageYOffset;
        window.scrollTo(0, csp - wd);
    });
}

function secret() { Swal.fire({ title: "Curious one you are.", width: 600, padding: "3em", background: "#fff url(https://picsum.photos/400/200)", backdrop: '\n    rgba(0,0,123,0.4)\n    url("/images/nyan-cat.gif")\n    center left\n    \n  ' }) }


$(document).ready(()=>{
    var activeRec;
  $('.rec-tab').click(function(){
    this.id = this.attributes[1].value;
  
    $($(activeRec).children()[0]).removeClass('active'); 
   
    activeRec = this;
    $($(this).children()[0]).addClass('active'); 
    fetch("getRecommendation.php?id="+this.id,{"method":"GET"})
      .then(response => response.json())
      .catch(error => console.warn(error))
      .then(function(response){
        console.log(response);
        if('#rec--name'.length > 0){
          $('#rec--name')[0].innerHTML = response.name;
        }
        if('#rec--message'.length > 0){
          $('#rec--message')[0].innerHTML = response.message;
        }
        if('#rec--image'.length > 0){
          $('#rec--image')[0].src = response.image;
        }
        if('#rec--heading'.length > 0){
          $('#rec--heading')[0].innerHTML = response.heading;
        }
          if('#rec--date'.length > 0){
          $('#rec--date')[0].innerHTML = response.date;
        }
      });
  })
  this.random = Math.floor(Math.random()*recCount);
  activeRec = $('.rec-tab')[this.random-1];
  $($('.rec-tab')[this.random-1].children[0]).addClass('active');
  fetch("getRecommendation.php?id="+this.random,{"method":"GET"})
      .then(response => response.json())
      .catch(error => console.warn(error))
      .then(function(response){
        console.log(response);
        if('#rec--name'.length > 0){
          $('#rec--name')[0].innerHTML = response.name;
        }
        if('#rec--message'.length > 0){
          $('#rec--message')[0].innerHTML = response.message;
        }
        if('#rec--image'.length > 0){
          $('#rec--image')[0].src = response.image;
        }
        if('#rec--heading'.length > 0){
          $('#rec--heading')[0].innerHTML = response.heading;
        } 
              if('#rec--date'.length > 0){
          $('#rec--date')[0].innerHTML = response.date;
        }
      });
})

