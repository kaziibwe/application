
/*
 * ==========================================================
 * MODULE SCRIPT
 * ==========================================================
 *
 * Perfex App Module main Javascript file. © 2021 board.support. All rights reserved.
 * 
 */

'use strict';

(function ($) {

    $(document).ready(function () {
        let menu = $('.menu-item-sb a');
        if ($(menu).attr('href').indexOf('admin.php') > 0) {
            $('.menu-item-sb a').attr('target', '_blank');
        }
    });

}(jQuery)); 
