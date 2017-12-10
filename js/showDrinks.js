

$('#categorySearch').on("keyup", function ()
{
    var template = $('#beverages-template').html();
    var location = $('#listDiv');

    function renderBeverages(data)
    {
        location.append(Mustache.render(template, data));
    }

var searchInput = $(this).val();
    var search_input = {
        // Gets value from drinkSearch
        categoryName: searchInput
    };
    $.ajax({
        type: 'POST',
        url: 'drinkSearch.php',
        data: search_input,
        dataType: 'json',
        success: function (categories) {
            location.empty();

            $.each(categories, function (i, category) {
                renderBeverages(category);
            });
        },
        error: function ()
        {
//            alert('error');
        }
    });

});

