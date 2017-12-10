$(document).ready(function(){
   
    $("#complBtn").click(function(){
        
       var orderID = $(this).data("orderid");
       
        var input = {option: 1, orderID: orderID};
        $.ajax({
            type: 'POST',
            url: '../dbMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
                if (response.result === "success")
                {
                    window.location.href = "orders.php";
                }
                else
                {
                    alert("error, did not update order");
                }
            },
            error: function ()
            {
                alert("error");
            },
            complete: function ()
            {
            }
        });
    });
    
    
    
});