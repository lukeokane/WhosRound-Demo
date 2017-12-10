$(document).ready(function () {

    var container = $(".itemMeasurements");
		var beveragesContainer = $("#listDiv");
    var template = $("#modalRowTemplate").html();

    $("#clearsearch").click(function () {
        $("#search").val("");
		
        var categoryID = $("#search").data("drinktypeid");

        var container = $("#listDiv");
        var template = $('#drinkTemplate').html();

        var input = {option: 2, drinkName: "", categoryID: categoryID};
        $.ajax({
            type: 'POST',
            url: 'dbMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
                if (response.result === "success")
                {
                    container.empty();

                    jQuery.each(response.results, function (i, result)
                    {
                        addThumbnails(container, result, template);
                    });
                }
                else
                {
//                    alert ("error");
                }
            },
            error: function ()
            {
//                alert ("error");
            },
            complete: function ()
            {
            }
        });

    });

    $("#searchicon").click(function () {
        $("#search").focus();
    });

    $("#search").keyup(function ()
    {
        // Drink search input & category ID 
        var searchInput = $(this).val();
        var categoryID = $(this).data("drinktypeid");

        var container = $("#listDiv");
        var template = $('#drinkTemplate').html();

        var input = {option: 2, drinkName: searchInput, categoryID: categoryID};
        $.ajax({
            type: 'POST',
            url: 'dbMethods.php',
            data: input,
            dataType: 'json',
            success: function (response)
            {
                if (response.result === "success")
                {
                    container.empty();

                    jQuery.each(response.results, function (i, result)
                    {
                        addThumbnails(container, result, template);
                    });
                }
                else
                {
//                    alert ("error");
                }
            },
            error: function ()
            {
//                alert ("error");
            },
            complete: function ()
            {
            }
        });
    });


    $('input').focus(function () {
        $('#previewOrder').hide();
        $('html body #listDiv').css("bottom", "0px");
    });

    $('input').focusout(function () {
        $('#previewOrder').show();
        $('html body #listDiv').css("bottom", "54px");
    });


    container.delegate('.quantityInput', 'focusin', function () {
        $(this).data('savePrev', $(this).val());
    });


    function addThumbnails(container, data, template)
    {
        container.append(Mustache.render(template, data));
    }

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

	    beveragesContainer.delegate(".element", "click", function () {

        var drinkID = $(this).data("drinkid");
        var drinkName = $(this).data("drinkname");
        var drinkAlcoholPerc = $(this).data("alcoholpercentage");
        var drinkImage = $(this).find('img').attr('src');

        $('.drinkName').empty();
        $('.drinkName').html(drinkName);

        $('.drinkPercentage').empty();
        $('.drinkPercentage').html(drinkAlcoholPerc + "%");

        $('.drinkSelectedImage').attr('src', drinkImage);


        var input = {drinkID: drinkID};
        $.ajax({
            type: 'POST',
            url: 'getDrinkMeasurements.php',
            data: input,
            dataType: 'json',
            success: function (measurements)
            {
                container.empty();
                $.each(measurements, function (i, measurement)
                {
                    $.each(itemBasket, function (i, item) {
                        if (measurement.drinkID == item.drinkID && measurement.drinkTypeID == item.drinkTypeID)
                        {
                            measurement.quantity = item.quantity;
                        }
                    });

                    addThumbnails(container, measurement, template);
                });
                $('#selectItem').modal('toggle');
                updateTotalPrice();
            },
            error: function ()
            {
            },
            complete: function ()
            {
            }
        });
    });

    $(".addToBasket").click(function () {
        $('#selectItem').modal('toggle');
    });

    $(".cancelAdd").click(function () {

        var drinkID = $(".itemMeasurements").find(".measurementInfo").data("drinkid");

        for (var i = 0; i < itemBasket.length; i++)
        {
            if (itemBasket[i].drinkID == drinkID)
            {
                itemBasket.splice(i, 1);
                i--;
            }
        }
        console.log(itemBasket);
        $('#selectItem').modal('toggle');

        updateCartNumber();

    });

    container.delegate('.specInstr', 'change', function () {
        addSpecialInstr(this);
    });


    container.delegate('.addIcon', 'click', function () {

        var $item = $(this).closest(".measurementInfo");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("price");

        var qtyBeforeChange = parseInt($item.find(".quantityInput").val());


        if (qtyBeforeChange >= 5000)
        {
            return;
        }

        $item.find(".quantityInput").val(qtyBeforeChange + parseInt(1));

        changeItem(drinkID, drinkTypeID, drinkPrice, 1, false);

        if (qtyBeforeChange === 0 && $item.find(".specInstr").val() !== "")

        {
            addSpecialInstr(this);
        }

        updateCartNumber();
        updateTotalPrice();
    });

    container.delegate('.quantityInput', 'keyup', function ()
    {
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
        var $item = $(this).closest(".measurementInfo");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("price");

        var qty = parseInt($(this).val());

        changeItem(drinkID, drinkTypeID, drinkPrice, qty, true);
		
		 if ($item.find(".specInstr").val() !== "")

        {
            addSpecialInstr(this);
        }

        updateCartNumber();
        updateTotalPrice();
        console.log(itemBasket);
    })

    container.delegate('.quantityInput', 'change', function ()
    {
        var $item = $(this).closest(".measurementInfo");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("price");

        if (isNaN(parseInt($(this).val())) || $(this).val() === "")
        {
            $(this).val(0);
            changeItem(drinkID, drinkTypeID, drinkPrice, $(this).val(), true);
            console.log(itemBasket);
        }
    });
	
		$("i.fa.fa-shopping-cart").click(function(){
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
	})
	
    container.delegate('.minusIcon', 'click', function () {

        var $item = $(this).closest(".measurementInfo");

        // Retrieve item data
        var drinkID = $item.data("drinkid");
        var drinkTypeID = $item.data("drinktypeid");
        var drinkPrice = $item.data("price");

        var qtyBeforeChange = parseInt($item.find(".quantityInput").val());

        if (qtyBeforeChange <= 0)
        {
            return;
        }


        $item.find(".quantityInput").val(qtyBeforeChange - parseInt(1));

        changeItem(drinkID, drinkTypeID, drinkPrice, -1, false);

        updateCartNumber();
        updateTotalPrice();

    });



    function addSpecialInstr(itemobject)
    {
        var $item = $(itemobject).closest(".measurementInfo");
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
        console.log(itemBasket);
    }

    function updateCartNumber()
    {
        var total = 0;
        jQuery.each(itemBasket, function (i, item)
        {
            total += item.quantity;
        });

        $("#basketAmtIcon").empty();
        $("#basketAmtIcon").append(total);
    }

    function updateTotalPrice()
    {

        var drinkID = $(".itemMeasurements").find(".measurementInfo").data("drinkid");

        var total = 0;

        jQuery.each(itemBasket, function (i, item) {
            if (item.drinkID === drinkID)
            {
                total += parseFloat(item.price);
            }
        });

        $("#basketTot").html("&euro;" + total.toFixed(2));
    }

    $("[data-link]").click(function (e) {

        var link = $(this).attr("data-link");
        e.preventDefault();
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
                window.location.href = link;
            }
        });
    });

    $("#profileSection").click(function () {
        if ($(this).data("si") == false)
        {
            var drinkType = $(this).data("drinktype")
            $('<form action="signinPage.php" method="post"><input type="hidden" name="nsiRelocation" value="drinkMenu.php?drinkType=' + drinkType + '"> </form>').appendTo('body').submit();
        } else
        {
            // do something else with clicking.
        }
    });
	
                $("#previewOrder").click(function () {
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
                window.location.href = "basket.php";
            }
        });
                });
});
