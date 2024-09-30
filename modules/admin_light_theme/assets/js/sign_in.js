document.addEventListener("DOMContentLoaded", function(event) { 
    "use strict";
    var col = document.getElementsByClassName("col-md-4")[0];
    var form = document.getElementsByTagName("form")[0];

    col.classList.remove("col-md-offset-4");
    col.classList.remove("col-sm-offset-2");
    col.classList.remove("col-md-4");
    col.classList.remove("col-sm-8");

    col.classList.add("col-md-6","col-md-offset-3");
    form.classList.add("col-xs-9");
    form.classList.add("col-sm-9");
    form.classList.add("col-md-9");

    document.getElementsByTagName('h1')[0].outerHTML = '<h2 class="col-md-12 col-sm-12 col-xs-12 text-center">Sign In</h2>';

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


    email_selector.before(login_icon);
    password_selector.before(password_icon);
    email_selector.after(underline_effect_one);
    password_selector.after(unerline_effect_two);

    var elementsArray =[email_selector, password_selector];
    elementsArray.forEach(function(elem) {
    	elem.addEventListener("focus", function(e) {
    		if((e.path[0].previousElementSibling.classList.contains('fa-user-circle-o'))){
    			login_icon.classList.add('icon_input_focus')
    		} 
    		if((e.path[0].previousElementSibling.classList.contains('fa-key'))){
    			password_icon.classList.add('icon_input_focus')
    		} 
    	});
    	elem.addEventListener("blur", function(e) {
    		if((e.path[0].previousElementSibling.classList.contains('fa-key'))){
    			password_icon.classList.remove('icon_input_focus')
    		} 
    		if((e.path[0].previousElementSibling.classList.contains('fa-user-circle-o'))){
    			login_icon.classList.remove('icon_input_focus')
    		} 
    	});
    });
});


