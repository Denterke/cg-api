/**
 * Created by kalita on 23/07/15.
 */
$(function() {
    $('button.submit-upload').click(function() {
        $('#upload-form').submit();
    });

    $('.upload').click(function() {
        var typeId = $(this).data('type'),
            typeDescription = $(this).data('description')
        ;
        $('#upload-file-label').text(typeDescription);
        $('#upload-type-input').val(typeId);
        $('#upload-modal').modal();
    });
});
