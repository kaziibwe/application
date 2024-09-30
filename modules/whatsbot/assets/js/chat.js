"use strict";
$(function () {
    $(".heading-compose").on('click', function () {
    $(".side-two").css({
      "left": "0",
      "top": "0",
      "position": "absolute"
    });
  });

    $(".newMessage-back").on('click', function () {
    $(".side-two").css({
      "left": "-100%"
    });
  });
})
