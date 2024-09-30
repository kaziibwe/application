$(function() {
        "use strict";


        $("#closebtn").on("click", function(e) {
                $('#mySidebar').css("width", "0");
                $('#main').css("margin-left", "0");

                $("#closebtn").hide();
                $("#openbtn").show();
        });

        $("#openbtn").on("click", function(e) {

                $('#mySidebar').css("width", "250px");
                $('#main').css("margin-left", "250px");

                $("#closebtn").show();
                $("#openbtn").hide();

        });

        var toc=$("#toc").tocify( {
                selectors: "h2,h3,h4,h5"
            }

        ).data("toc-tocify");
});