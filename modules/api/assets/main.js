"use strict";

function new_user() {
    appValidateForm($('form'), {
        user: 'required',
        name: 'required',
        expiration_date: 'required'
    });
    $('#user_api').modal('show');
    $('.edit-title').addClass('hide');
    $('#user_api input[name="user"]').val('');
    $('#user_api input[name="name"]').val('');
    $('#user_api input[name="expiration_date"]').val('');
}

function edit_user(invoker, id) {
    appValidateForm($('form'), {
        user: 'required',
        name: 'required',
        expiration_date: 'required'
    });
    var user = $(invoker).data('user');
    var name = $(invoker).data('name');
    var expiration_date = $(invoker).data('expiration_date');
    $('#additional').append(hidden_input('id', id));
    $('#user_api input[name="user"]').val(user);
    $('#user_api input[name="name"]').val(name);
    $('#user_api input[name="expiration_date"]').val(expiration_date);
    $('#user_api').modal('show');
    $('.add-title').addClass('hide');
}