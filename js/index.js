$(document).ready(function ()
{
  

    $('.element').click(function () {
        var estabID = $(this).data("establishmentid");
        
        //alert(estabID);
        var input = {establishmentID: estabID};

        $.ajax({
            type: 'POST',
            url: 'setEstab.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
                if (response.result === "success")
                {
                    window.location.href = "drinkCategory.php";
                }
            },
            error: function ()
            {
            },
            complete: function ()
            {
              
            }
        });
    });
    
    $("#profileSection").click(function () {
        if ($(this).data("si") == false)
        {
            $('<form action="signinPage.php" method="post"><input type="hidden" name="nsiRelocation" value="index.php"> </form>').appendTo('body').submit();
        } else
        {
            // do something else with clicking.
        }
    });
	
	$(".fa-shopping-cart").click(function() {
		if ($(this).data("estabset") == true)
		{
				window.location.href = "basket.php";
		}
	});
	
});
    