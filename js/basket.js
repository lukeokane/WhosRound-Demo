$(document).ready(function () {

    $(".addIcon").on("click", function () {
        var $item = $(this).closest(".item.row");

        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("drinkprice");

        var qtyBeforeChange = parseInt($item.find(".quantityInput").val());
        if (qtyBeforeChange >= 5000)
        {
            return;
        }

        $item.find(".quantityInput").val(parseInt(qtyBeforeChange) + parseInt(1));

        changeItem(drinkID, drinkTypeID, drinkPrice, 1, false);

           
        if (qtyBeforeChange === 0 && $item.find(".specInstr").val() !== "")
        {
            console.log("in");
            addSpecialInstr(this);
        }

        basketQuantityCount();
        $(".basketTot").html("&euro;" + updateTotal());
        $(".basketSubTot").html("&euro;" + updateSubTotal());
        console.log(itemBasket);
    });
    
    
        $('#backbutton').click(function () {

        var backbuttonlink = "drinkCategory.php";
        var input = {option: 4, basketJSON: JSON.stringify(itemBasket)};
        $.ajax({
            type: 'POST',
            url: 'basketMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
            },
            error: function ()
            {
            },
            complete: function ()
            {
                window.location.href = backbuttonlink;
            }
        });
    });


    $(".minusIcon").on("click", function () {

        var $item = $(this).closest(".item.row");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("drinkprice");

        var qtyBeforeChange = parseInt($item.find(".quantityInput").val());

        if (qtyBeforeChange <= 0)
        {
            return;
        }

        $item.find(".quantityInput").val(qtyBeforeChange - parseInt(1));

        changeItem(drinkID, drinkTypeID, drinkPrice, -1, false);

        basketQuantityCount();
        $(".basketTot").html("&euro;" + updateTotal());
        $(".basketSubTot").html("&euro;" + updateSubTotal());
        console.log(itemBasket);
    });

    $(".specInstr").change(function () {
        addSpecialInstr(this);
    });

    $('.quantityInput').on('focusin', function () {
        $(this).data('savePrev', $(this).val());
		
		$("footer").hide();
    });
	
	    $('input').focus(function () {
        $('footer').hide();
        $('html body div.container-fluid.body-content').css("bottom", "0px");
    });

    $('input').focusout(function () {
        $('footer').show();
        $('html body div.container-fluid.body-content').css("bottom", "210px");
    });

    $('.quantityInput').on('change', function () {
        if ($(this).val() === "")
        {
            $(this).val(0);
        }
    });

    $('.quantityInput').on('keyup', function () {
        if (isNaN(parseInt($(this).val())))
        {
            if ($(this).val() === "")
            {
                return;
            } else
            {
                $(this).val(0);
                return;
            }
        }

        if (parseInt($(this).val()) < 0)
        {
            $(this).val(0);
        }
        var $item = $(this).closest(".item.row");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("drinkprice");

        var qty = parseInt($(this).val());

        changeItem(drinkID, drinkTypeID, drinkPrice, qty, true);

        basketQuantityCount();
        $(".basketTot").html("&euro;" + updateTotal());
        $(".basketSubTot").html("&euro;" + updateSubTotal());
        console.log(itemBasket);
    });




    function addSpecialInstr(itemobject)
    {
        var $item = $(itemobject).closest(".item.row");
        var quantity = $item.find(".quantityInput").val();

        if (quantity <= 0)
        {
            return;
        }

        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");

        var specialInstr = $item.find(".specInstr").val();

        jQuery.each(itemBasket, function (i, item) {
            if (item.drinkID === drinkID && item.drinkTypeID === drinkTypeID)
            {
                item.speciInstr = specialInstr;
            }
        });

        console.log(itemBasket);
    }
	
		$("i.fa.fa-shopping-cart").click(function(){
		if (basketQuantityCount() > 0)
        {
           var basketLink = "basket.php";
        var input = {option: 4, basketJSON: JSON.stringify(itemBasket)};
        $.ajax({
            type: 'POST',
            url: 'basketMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
            },
            error: function ()
            {
            },
            complete: function ()
            {
                window.location.href = basketLink;
            }
        });
        }
	})


    function changeItem(drinkID, drinkTypeID, price, quantity, overwrite)
    {

        var exists = false;

        for (var i = 0; i < itemBasket.length; i++)
        {
            if (itemBasket[i].drinkID === drinkID && itemBasket[i].drinkTypeID === drinkTypeID)
            {
                if (overwrite === true)
                {
                    itemBasket[i].quantity = quantity;
                } else
                {
                    itemBasket[i].quantity += quantity;
                }

                // Remove item if quantity is 0.
                if (itemBasket[i].quantity == 0)
                {
                    itemBasket.splice(i, 1);

                    return false;
                }
                itemBasket[i].price = price * itemBasket[i].quantity;

                exists = true;

                // returning false stops the loop, not the function itself.
                return false;
            }
        }

        jQuery.each(itemBasket, function (i, item) {

            if (item.drinkID === drinkID && item.drinkTypeID === drinkTypeID)
            {
                if (overwrite === true)
                {
                    item.quantity = quantity;
                } else
                {
                    item.quantity += quantity;
                }

                // Remove item if quantity is 0.
                if (item.quantity == 0)
                {
                    itemBasket.splice(i, 1);
                }
                item.price = price * item.quantity;

                exists = true;
                // returning false stops the loop, not the function itself.
                return false;
            }
        });

        // Add new basket item if exits foreach loop.
        if (!exists)
        {
            var priceCalc = quantity * price;
            itemBasket.push({drinkID: drinkID, drinkTypeID: drinkTypeID, quantity: quantity, price: priceCalc, speciInstr: ""});

        }
    }


    function basketQuantityCount()
    {
        var total = 0;
        jQuery.each(itemBasket, function (i, item)
        {
            total += item.quantity;
        });

        $("#basketAmtIcon").empty();
        $("#basketAmtIcon").append(total);
        return total;
        console.log(itemBasket);
    }

    function updateTotal()
    {
        var subTotal = updateSubTotal();

        var total = parseFloat(subTotal) + parseFloat(0.10);

        return total.toFixed(2);
    }

    function updateSubTotal()
    {
        var total = 0;


        jQuery.each(itemBasket, function (i, item) {

            total += parseFloat(item.price);
        });

        return total.toFixed(2);
    }

    $('#completeOrder').click(function (){
        if (basketQuantityCount() > 0)
        {
           var confirmPaymentLink = "confirmPayment.php";
        var input = {option: 4, basketJSON: JSON.stringify(itemBasket)};
        $.ajax({
            type: 'POST',
            url: 'basketMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
            },
            error: function ()
            {
            },
            complete: function ()
            {
                window.location.href = confirmPaymentLink;
            }
        });
        }
    });
    
    $("#profileSection").click(function () {
        if ($(this).data("si") == false)
        {
            $('<form action="signinPage.php" method="post"><input type="hidden" name="nsiRelocation" value="basket.php"> </form>').appendTo('body').submit();
        } else
        {
            // do something else with clicking.
        }
    });
	
	  $(".deleteIcon").on("click", function () {

        var $item = $(this).closest(".item.row");


        $(this).remove();
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");

        var quantity = $item.find(".quantityInput").val();
        			
        $item.remove();
		
        changeItem(drinkID, drinkTypeID, 0, 0, true);

        basketQuantityCount();
        $(".basketTot").html("&euro;" + updateTotal());
        $(".basketSubTot").html("&euro;" + updateSubTotal());
        console.log(itemBasket);
    });

});
