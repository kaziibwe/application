(function($) {
    "use strict";
    $('body').addClass('admin_light_theme_initiated');
    // Add butons wave effects
    Waves.init();
    Waves.attach('.btn', ['waves-effect', 'waves-light', 'waves-ripple']);

    // Change chart default font color
    Chart.defaults.global.defaultFontColor = "#000000";

    // Ini nanobar
    var options = {
        target: document.getElementsByTagName("BODY")[0]
    };

    var nanobar = new Nanobar(options);
    if (document.readyState == 'loading') {
        nanobar.go(30);
        nanobar.go(76);
        nanobar.go(100);
    }

})(jQuery);
document.addEventListener("DOMContentLoaded", function(event) {
    "use strict";
    var form = document.getElementsByTagName("form")[0];

    var email_selector = document.getElementById("email");
    var password_selector = document.getElementById("password");

    var underline_effect_one = document.createElement('div');
    var unerline_effect_two = document.createElement('div');

    var login_icon = document.createElement('i');
    var password_icon = document.createElement('i');

    underline_effect_one.className = 'line_toggler';
    unerline_effect_two.className = 'line_toggler';
    login_icon.className = 'fa fa-user-circle-o login_icon';
    password_icon.className = 'fa fa-key login_icon';


    if (email_selector) {
        email_selector.before(login_icon);
        email_selector.after(underline_effect_one);
    }
    if (password_selector) {
        password_selector.before(password_icon);
        password_selector.after(unerline_effect_two);
    }
    if (email_selector && password_selector) {
        var elementsArray = [email_selector, password_selector];
        elementsArray.forEach(function(elem) {
            elem.addEventListener("focus", function(e) {
                if ((e.path[0].previousElementSibling.classList.contains('fa-user-circle-o'))) {
                    login_icon.classList.add('icon_input_focus')
                }
                if ((e.path[0].previousElementSibling.classList.contains('fa-key'))) {
                    password_icon.classList.add('icon_input_focus')
                }
            });
            elem.addEventListener("blur", function(e) {
                if ((e.path[0].previousElementSibling.classList.contains('fa-key'))) {
                    password_icon.classList.remove('icon_input_focus')
                }
                if ((e.path[0].previousElementSibling.classList.contains('fa-user-circle-o'))) {
                    login_icon.classList.remove('icon_input_focus')
                }
            });
        });
    }
});