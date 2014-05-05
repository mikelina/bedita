// used in view group details

var urlAddObjToAssoc = BEDITA.base + 'pages/loadObjectToAssoc';

function addObjToAssoc(url, postdata) {
    postdata.tplname = 'elements/form_group_permissions';
    $("#loadingDownloadRel").show();
    $.post(url, postdata, function(html){
        $("#loadingDownloadRel").hide();
        // add row
        $("table.group_objects").append(html);
        $("table.group_objects select[multiple]").chosen({width: '95%'});
    });
}

$(document).ready(function() {

    $(document).on('click', 'table.group_objects input[name=remove]', function() {
        $(this).parents('tr:first').remove();
    });

    $('#authselect').change(function() {
        var value = $(this).val();
        $('.authTypeForm').hide();
        $('.authTypeForm#authType'+capitaliseFirstLetter(value)).show();
    });

});