(function($) {
    "use strict"; // Start of use strict

    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: (target.offset().top - 54)
                }, 1000, "easeInOutExpo");
                return false;
            }
        }
    });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function() {
        $('.navbar-collapse').collapse('hide');
    });

    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
        target: '#mainNav',
        offset: 54
    });

    //Accordion Script 
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }

    //Accordion Script End 
    // jQuery starts here for the contact form
    $(function() {

        // Get the form.
        var form = $('#contact');

        // Get the messages div.
        var formMessages = $('#form-messages');

        // Set up an event listener for the contact form.
        $(form).submit(function(e) {

            // Stop the browser from submitting the form.
            e.preventDefault();

            // Serialize the form data.

            var formData = {};
            formData['name'] = $("#name").val();
            formData['email'] = $("#email").val();
            formData['subject'] = $("#subject").val();
            formData['message'] = $("#message").val();

            console.log(formData);

            // Submit the form using AJAX.
            $.ajax({
                    type: 'POST',
                    url: './mailer.php',
                    data: formData
                })
                .done(function(response) {
                    // Make sure that the formMessages div has the 'success' class.
                    $(formMessages).removeClass('error');
                    $(formMessages).addClass('success');

                    // Set the message text.
                    $(formMessages).text(response);

                    // Clear the form.
                    $('#name').val('');
                    $('#email').val('');
                    $('#message').val('');
                    $('#subject').val('');

                })
                .fail(function(data) {
                    // Make sure that the formMessages div has the 'error' class.
                    $(formMessages).removeClass('success');
                    $(formMessages).addClass('error');

                    // Set the message text.
                    if (data.responseText !== '') {
                        $(formMessages).text(data.responseText);
                    } else {
                        $(formMessages).text('Oops! An error occured and your message could not be sent.');
                    }
                });

        });

    });
    // jQuery ends here for the contact form

    (function() {
        var items = document.querySelectorAll(".timeline li");

        function myviewport(el) {
            var rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        function mycallback() {
            for (var i = 0; i < items.length; i++) {
                if (myviewport(items[i])) {
                    items[i].classList.add("in-view");
                }
            }
        }
        window.addEventListener("load", mycallback);
        window.addEventListener("resize", mycallback);
        window.addEventListener("scroll", mycallback);
    })();



})(jQuery); // End of use strict