function $(id) {
    return document.getElementById(id);
}
function su(v) {
    var ext = v.split('.').pop().toLowerCase();
    if (!v.match(/(jpg|jpeg|gif|png)$/i)) {
        alert('Bitte nur die erlaubten Dateitypen auswählen!');
        return false;
    }
    $('ubh').style.display = 'none';
    $('ubh2').style.display = 'none';
    $('ubt').style.display = '';
    $('frm').submit();
    return true;
}
function s(o) {
    if (o.value == 'f') {
        $('ih').style.display = '';
    } else {
        $('ih').style.display = 'none';
    }
}
//
function bc() {
    $('btn').blur();
    if ($('r1').checked)
        return false;
    if ($('r2').checked) {
        var u = prompt('Bitte die Webadresse eingeben:', 'http://');
        if (!u)
            return false;
    }
    if ($('r3').checked) {
        var u = prompt('Bitte den Text oder URL eingeben:', '');
        if (!u)
            return false;
        u = 'http://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=' + encodeURIComponent(u);
    }
    $('url').value = u;
    $('ubh').style.display = 'none';
    $('ubh2').style.display = 'none';
    $('ubk').style.display = '';
    $('frm2').submit();
    return false;
}