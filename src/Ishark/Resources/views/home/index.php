<?php $this->layout('layout') ?>

<div id="txt1">
    Ein-Klick-Bilderspeicher. Maximale Dateigröße:
    <strong>5 Megabyte</strong>. <br/>
    Erlaubte Dateitypen:
    <strong>JPEG, PNG und GIF</strong>
</div>
<form action="/upload" method="post" enctype="multipart/form-data" id="frm">
    <div id="ub">
        <span id="ubt" style="display: none;">Lade hoch...</span>
        <span id="ubk" style="display: none;">Kopiere Bild...</span>
        <br/>

        <div id="ubh">
            <button id="btn" onclick="return bc()">Datei <u>h</u>ochladen ...</button>
            <div class="ih" id="ih">
                <input type="file" name="img" accesskey="h" accept="image/jpeg,image/gif,image/png" onchange="su(this.value)"/>
            </div>
        </div>
        <br clear="all"/>

        <div id="ubh2">
            <input type="radio" id="r1" name="ult" value="f" checked="checked" accesskey="d" onchange="s(this)"/>
            <label for="r1"> <u>D</u>atei</label>
            &nbsp;
            <input type="radio" id="r2" name="ult" value="u" accesskey="w" onchange="s(this)"/>
            <label for="r2"> <u>W</u>ebadresse</label>
        </div>
    </div>
</form>
<form action="/uploadURL" method="post" id="frm2">
    <input type="hidden" name="url" id="url"/>
</form>