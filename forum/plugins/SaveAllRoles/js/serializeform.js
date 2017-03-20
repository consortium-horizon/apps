$(function() {
    $('form[method="post"]').submit(function() {
        if ($('textarea[name="serialized_data"]', this).length > 0) {
            return false;
        }
        var data = $(this).serialize();
        $(this).find('input, select, textarea').not('[type="submit"], [type="file"]').attr("disabled", true);
        $(this).append('<input type="hidden" name="TransientKey" value="' + gdn.definition('TransientKey') + '">'); 
        $(this).append('<textarea style="display:true" name="serialized_data">' + data + '</textarea>');
        return true;
    });
});
