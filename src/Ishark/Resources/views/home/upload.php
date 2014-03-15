<?php $this->layout('layout') ?>


<div id="txt1">
    <h1>Upload fertig.</h1><br />
    <img width="100" height="100" src="http://<?php echo $this->domain; ?>/<?php echo $this->file; ?>" alt="Hochgeladenes Bild." />
    <label for="url1">Direktlink</label>
    <input type="text" id="url1" onclick="select()" value="http://<?php echo $this->domain; ?>/<?php echo $this->file; ?>" /><br />

    <!--
    <label for="url5">Link zum Bild für ESL.EU</label>
    <input type="text" id="url5" onclick="select()" value="url[http://ishark.tk/FgO-rLVAH1o910Wlux9RRQ.jpg][Beschreibungstext]url" /><br />
    <label for="url2">Bild für Foren</label>
    <input type="text" id="url2" onclick="select()" value="[img]http://ishark.tk/FgO-rLVAH1o910Wlux9RRQ.jpg[/img]" /><br />
    <label for="url3">Miniaturbild mit Link zum Bild für Foren</label>
    <input type="text" id="url3" onclick="select()" value="[url=http://ishark.tk/FgO-rLVAH1o910Wlux9RRQ.jpg][img]http://ishark.tk/thumb-FgO-rLVAH1o910Wlux9RRQ.jpg[/img][/url]" /><br />
    <label for="url4">Miniaturbild mit Link für eBay™</label>
    <input type="text" id="url4" onclick="select()" value='&lt;a href="http://ishark.tk/FgO-rLVAH1o910Wlux9RRQ.jpg" target="_blank"&gt;&lt;img src="http://ishark.tk/thumb-FgO-rLVAH1o910Wlux9RRQ.jpg" alt="" /&gt;&lt;/a&gt;' /><br />
    <br />
    -->
    <br /><button onclick="location='.'">Zurück</button>
</div>