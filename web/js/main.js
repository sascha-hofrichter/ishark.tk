function su(v) {
    if (!v.match(/(jpg|jpeg|gif|png)$/i)) {
        alert('Bitte nur die erlaubten Dateitypen auswählen!');
        return false;
    }
    $('#ubh').css('display', 'none');
    $('#ubh2').css('display', 'none');
    $('#ubt').css('display', '');
    $('#frm').submit();
    return true;
}
function s(o) {
    if (o.value == 'f') {
        $('#ih').css('display', '');
    } else {
        $('#ih').css('display', 'none');
    }
}
//
function bc() {
    $('#btn').blur();
    if ($('#r1').prop('checked'))
        return false;
    if ($('#r2').prop('checked')) {
        var u = prompt('Bitte die Webadresse eingeben:', 'http://');
        if (!u)
            return false;
    }
    $('#url').val(u);
    $('#ubh').css('display', 'none');
    $('#ubh2').css('display', 'none');
    $('#ubk').css('display', '');
    $('#frm2').submit();
    return false;
}