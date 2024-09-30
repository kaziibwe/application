"use strict";

function approve_booking(booking){
  $.post(admin_url+'resourcebooking/check_approve_booking/'+booking).done(function(response){
     response = JSON.parse(response);
     if(response.check == true){
        $.post(admin_url+'resourcebooking/approve_booking/2/'+booking).done(function(){
           location.reload();
        });
     }else{
        if(!empty(response.list_sending)){
           $('#list_sending').modal('show');
           $('#list').html('');
           $('#list').append(response.list_sending);
           $('#input').html('');
           $.each(response.list_id_sending,function(){
              $('#input').append('<input type="hidden" name="list_reject[]" id="'+this+'" value="'+this+'">');
           });
        }else{
           $('#list_approve').modal('show');
           $('#approved').html('');
           $('#approved').append(response.list_approved);
        }
     }
  });
}
$("body").on('click focus', '#booking_comment', function(e) {
  init_new_booking_comment();
});
Dropzone.autoDiscover = false;
function init_new_booking_comment(manual) {

    if (tinymce.editors.booking_comment) {
        tinymce.remove('#booking_comment');
    }

    if (typeof(bookingCommentAttachmentDropzone) != 'undefined') {
        bookingCommentAttachmentDropzone.destroy();
    }

    $('#dropzoneBookingComment').removeClass('hide');
    $('#addBookingCommentBtn').removeClass('hide');
    var $booking = $('#view_booking_comment');
        
    bookingCommentAttachmentDropzone = new Dropzone("#booking-comment-form", appCreateDropzoneOptions({
        uploadMultiple: true,
        clickable: '#dropzoneBookingComment',
        previewsContainer: '.dropzone-booking-comment-previews',
        autoProcessQueue: false,
        addRemoveLinks: true,
        parallelUploads: 20,
        maxFiles: 20,
        paramName: 'file',
        sending: function(file, xhr, formData) {
            formData.append("booking", $('#addBookingCommentBtn').attr('data-comment-booking-id'));
            if (tinyMCE.activeEditor) {
                formData.append("content", tinyMCE.activeEditor.getContent());
            } else {
                formData.append("content", $('#booking_comment').val());
            }
        },
        success: function(files, response) {
            response = JSON.parse(response);
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                $booking.html(response.taskHtml);
                tinymce.remove('#booking_comment');
            }
        }
    }));
    var editorConfig = _simple_editor_config();
    if (typeof(manual) == 'undefined' || manual === false) {
        editorConfig.auto_focus = true;
    }
    var iOS = is_ios();
    // Not working fine on iOs
    if (!iOS) {
        init_editor('#booking_comment', editorConfig);
    }
}
function add_booking_comment(booking) {
    var data = {};

    if (bookingCommentAttachmentDropzone.files.length > 0) {
        bookingCommentAttachmentDropzone.processQueue(booking);
        return;
    }
    if (tinymce.activeEditor) {
        data.content = tinyMCE.activeEditor.getContent();
    } else {
        data.content = $('#booking_comment').val();
        data.no_editor = true;
    }
    data.booking = booking;
    $.post(admin_url + 'resourcebooking/add_booking_comment', data).done(function(response) {
        response = JSON.parse(response);
        var $booking = $('#view_booking_comment');
        $booking.html(response.taskHtml);
        tinymce.remove('#booking_comment');
    });
}
function edit_task_comment(id) {
    var edit_wrapper = $('[data-edit-comment="' + id + '"]');
    edit_wrapper.next().addClass('hide');
    edit_wrapper.removeClass('hide');

    if (!is_ios()) {
        tinymce.remove('#task_comment_' + id);
        var editorConfig = _simple_editor_config();
        editorConfig.auto_focus = 'task_comment_' + id;
        init_editor('#task_comment_' + id, editorConfig);
        tinymce.triggerSave();
    }
}

// Cancel editing commment after clicked on edit href
function cancel_edit_comment(id) {
    var edit_wrapper = $('[data-edit-comment="' + id + '"]');
    tinymce.remove('[data-edit-comment="' + id + '"] textarea');
    edit_wrapper.addClass('hide');
    edit_wrapper.next().removeClass('hide');
}

// Save task edited comment
function save_edited_comment(id, task_id) {
    tinymce.triggerSave();
    var data = {};
    data.id = id;
    data.booking = task_id;
    data.content = $('[data-edit-comment="' + id + '"]').find('textarea').val();
    if (is_ios()) {
        data.no_editor = true;
    }
    $.post(admin_url + 'resourcebooking/edit_comment', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
           var $booking = $('#view_booking_comment');
        $booking.html(response.taskHtml);
        } else {
            cancel_edit_comment(id);
        }
        tinymce.remove('[data-edit-comment="' + id + '"] textarea');
    });
}
function remove_task_comment(commentid) {
    if (confirm_delete()) {
        requestGetJSON('resourcebooking/remove_comment/' + commentid).done(function(response) {
            if (response.success === true || response.success == 'true') {
                $('[data-commentid="' + commentid + '"]').remove();
                $('[data-comment-attachment="' + commentid + '"]').remove();
                _task_attachments_more_and_less_checks();
            }
        });
    }
}
function _booking_attachments_more_and_less_checks() {
    var att_wrap = $("body").find('.task_attachments_wrapper');
    var attachments = att_wrap.find('.task-attachment-col');
    var taskAttachmentsMore = $("body").find('#show-more-less-task-attachments-col .task-attachments-more');
    if (attachments.length === 0) {
        att_wrap.remove();
    } else if (attachments.length == 2 && taskAttachmentsMore.hasClass('hide')) {
        $("body").find('#show-more-less-task-attachments-col').remove();
    } else if ($('.task_attachments_wrapper .task-attachment-col:visible').length === 0 && !taskAttachmentsMore.hasClass('hide')) {
        taskAttachmentsMore.on('click');
    }

    $.each($('#task-modal .comment-content'), function() {
        if ($(this).find('.task-attachment-col').length === 0) {
            $(this).find('.download-all').remove();
        }
    });
}

// Removes task single attachment
function remove_booking_attachment(link, id) {
    if (confirm_delete()) {
        requestGetJSON('resourcebooking/remove_booking_attachment/' + id).done(function(response) {
            if (response.success === true || response.success == 'true') { $('[data-task-attachment-id="' + id + '"]').remove(); }
            _booking_attachments_more_and_less_checks();
            if (response.comment_removed) {
                $('#comment_' + response.comment_removed).remove();
            }
        });
    }
}