"use strict";
Dropzone.autoDiscover = false;
Dropzone.options.bannerDropzone = false;

function convertToDate(inputDate) {
    // List of supported date formats
    var formats = [
        "DD-MM-YYYY",
        "DD/MM/YYYY",
        "MM-DD-YYYY",
        "MM.DD.YYYY",
        "MM/DD/YYYY",
        "YYYY-MM-DD",
        "DD.MM.YYYY"
    ];

    // Loop through each format and try to parse the input date
    for (var i = 0; i < formats.length; i++) {
        var parsedDate = moment(inputDate, formats[i], true);
        if (parsedDate.isValid()) {
            return parsedDate.format("YYYY-MM-DD");
        }
    }

    // If none of the formats match, return an error message
    return "Invalid Date Format";
}

$.validator.addMethod("enddate", function (value, element) {
    var startDateValue = convertToDate($('#start_date').val());
    var currentValue = convertToDate(value);
    return Date.parse(startDateValue) <= Date.parse(currentValue);
}, 'End Date should be greater than equal to Start Date.');

function openCropImagePopup() {
    const cropImagePopup = $('#crop_image_modal');

    cropImagePopup.modal('show');

    const image = document.getElementById('image');
    var cropBoxData;
    var canvasData;
    var cropper;

    cropper = new Cropper(image, {
        viewMode: 2,
        aspectRatio: 1600 / 300,
        minContainerWidth: 1465,
        minContainerHeight: 300,
        ready: function () {
            cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
        }
    });

    document.getElementById('cropButton').addEventListener('click', function () {
        const croppedCanvas = cropper.getCroppedCanvas({
            width: 1600,
            height: 300
        });

        const croppedImageDataURL = croppedCanvas.toDataURL('image/jpeg');

        $.ajax({
            type: 'POST',
            url: `${admin_url}banner/saveCroppedImage`,
            data: { image: croppedImageDataURL, image_name: $('#image').data('imagename') },
            dataType: 'json'
        }).done(function (res) {
            cropBoxData = cropper.getCropBoxData();
            canvasData = cropper.getCanvasData();
            cropper.destroy();
            cropImagePopup.modal('hide');
            window.location.reload();
        });
    });
}

$(function () {
    initDataTable('.table-banner-details', `${admin_url}banner/getTableData`);

    if ($('#dropzoneDragArea').length > 0) {
        var bannerDropzone = new Dropzone("#banner-image-form", appCreateDropzoneOptions({
            autoProcessQueue: false,
            clickable: '#dropzoneDragArea',
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 1,
            acceptedFiles: '.jpg, .jpeg, .png, .bmp, .webp',
            success: function (file, response) {
                response = JSON.parse(response);
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length ===
                    0) {
                    window.location.assign(response.url);
                }
            },
        }));
    }

    appValidateForm($('#banner-image-form'), {
        title: "required",
        start_date: {
            required: true
        },
        end_date: {
            required: true,
            enddate: true
        }
    }, saveBannerImage);


    function saveBannerImage(form) {
        $("#save-banner-image").prop("disabled", true);
        $.ajax({
            url: `${admin_url}banner/addBannerImage`,
            type: 'POST',
            dataType: 'json',
            data: $(form).serialize()
        }).done(function (res) {
            if (typeof (bannerDropzone) !== 'undefined') {
                if (bannerDropzone.getQueuedFiles().length > 0) {
                    bannerDropzone.options.url = `${admin_url}banner/addBannerImage`;
                    bannerDropzone.processQueue();
                } else {
                    window.location.assign(res.url);
                }
            } else {
                window.location.assign(res.url);
            }
        });
    }

    $('#banner-image-form #start_date').on('change', function () {
        var d1 = convertToDate($(this).val());

        $('#banner-image-form #end_date').attr("data-date-min-date", d1).trigger("focusout");
        setTimeout(() => {
            init_datepicker();
        }, 1000);
    });

    $('body').on('change', '#has_action', function(event) {
        if ($(this).is(':checked')) {
            $('.has_action').removeClass('hide');
        } else {
            $('.has_action').addClass('hide');
            $('input[name="action_label"], input[name="action_url"]').val('');
            $('#action_target').prop('checked', false);
        }
    });
});