$(document).ready(function () {

    $("#clearsearch").click(function () {
        $("#search").val("");
    });

    $("#searchicon").click(function () {
        $("#search").focus();
    });

    $('#backbutton').click(function () {

        var backbuttonlink = "index.php";
        window.location.href = backbuttonlink;

    });
    
    $("#listDiv").delegate(".element", "click", function()
    {
        var link = $(this).data("link");
        window.location.href = link;
    });

    $("#profileSection").click(function () {
        if ($(this).data("si") == false)
        {
            $('<form action="signinPage.php" method="post"><input type="hidden" name="nsiRelocation" value="drinkCategory.php"> </form>').appendTo('body').submit();
        } else
        {
            // do something else with clicking.
        }
    });
	
			$("i.fa.fa-shopping-cart").click(function(){
				var basketlink = "basket.php";
                window.location.href = basketlink;
	});
	
});