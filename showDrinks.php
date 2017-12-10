<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>jQuery Sample</title>

        <style>
            #beverage-container
            {
               margin-top: 15%;
            }
            .col
            {
                float: left;
                width: 24%;
                border: 1px solid green;
            }

            .row{
                
                height: auto;
                overflow: auto;
            }
        </style>
    </head>
    <body>
 
  <input type="text" id="drinkSearch" name="drink" placeholder="Drink Name"><br>
        
  
        <div id="beverage-container">
            
        </div>

        <template id="beverages-template">
            <div class="row">
                <div class="col">{{name}}</div>
                <div class="col">{{alcoholPercentage}}</div>
                <div class="col">{{calories}}</div>
                <div class="col">{{categoryName}}</div>
            </div>
        </template>

        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
        <script src="js/mustache.js"></script>
        <script src="js/showDrinks.js"></script>
    </body>
</html>
