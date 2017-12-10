$(document).ready(function ()
    {
        var $sign_in_alert_div = $("#sign_in_alerts");
        var $sign_in_button = $('#signinbutton');
        var $loading = $('.loading');

        $sign_in_button.on("click", function ()
            {
                var search_input = {customerUsername: $('#name').val(), customerPassword: $('#password').val()};
                $.ajax({
                    type: 'POST',
                    url: 'login.php',
                    data: search_input,
                    dataType: 'json',
                    success: function (response)
                        {
                            if (response.result === "input_error")
                                {
                                    $sign_in_alert_div.empty();
                                    $sign_in_alert_div.append(response.response);
                                } 
                                else if (response.result === "technical_error")
                                {

                                    $sign_in_alert_div.empty();
                                    $sign_in_alert_div.css("color", "red");
                                    $sign_in_alert_div.append("There is a problem connecting with the server...");
                                    ;
                                } 
                                else if (response.result === "success")
                                {
                                    $sign_in_alert_div.empty();
                                    $sign_in_alert_div.css("color", "green");
                                    $sign_in_alert_div.append("Correct! Signing in...");
                                    window.location.href = "index.php";
                                }
                        },
                    error: function ()
                        {
                            $sign_in_alert_div.empty();
                            $sign_in_alert_div.css("color", "red");
                            $sign_in_alert_div.append("There is a problem connecting with the server...");
                        },
                    complete: function ()
                        {
                            $sign_in_alert_div.fadeOut();
                            $sign_in_alert_div.fadeIn();
                            $loading.fadeOut(1000);
                            $sign_in_alert_div.css("color", "black");
                        }
                });
            });
    });
    