$(document).ready(function ()
    {
        var $sign_up_alert_div = $("#sign_up_alerts");
        var $sign_up_button = $('#signupbutton');
        var $loading = $('.loading');

        $sign_up_button.on("click", function ()
            {
                var search_input = {customerUsername: $('#name').val(), customerEmail: $('#email').val(),
                    customerPassword1: $('#password').val(), customerPassword2: $('#passwordAgain').val()};
                $.ajax({
                    type: 'POST',
                    url: 'signup.php',
                    data: search_input,
                    dataType: 'json',
                    success: function (response)
                        {
                            if (response.result === "input_error")
                                {
                                    $sign_up_alert_div.empty();
                                    $sign_up_alert_div.append(response.response);
                                } 
                                else if (response.result === "technical_error")
                                {

                                    $sign_up_alert_div.empty();
                                    $sign_up_alert_div.css("color", "red");
                                    $sign_up_alert_div.append("There is a problem connecting with the server...");
                                    ;
                                } 
                                else if (response.result === "success")
                                {
                                    $sign_up_alert_div.empty();
                                    $sign_up_alert_div.css("color", "green");
                                    $sign_up_alert_div.append("Registration successful...");
                                    window.location.href = "signinPage.php";
                                }
                        },
                    error: function ()
                        {
                            $sign_up_alert_div.empty();
                            $sign_up_alert_div.css("color", "red");
                            $sign_up_alert_div.append("There is a problem connecting with the server...");
                        },
                    complete: function ()
                        {
                            $sign_up_alert_div.fadeOut();
                            $sign_up_alert_div.fadeIn();
                            $loading.fadeOut(1000);
                            $sign_up_alert_div.css("color", "black");
                        }
                });
            });
    });
    