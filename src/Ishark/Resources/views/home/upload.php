<?php $this->layout('layout') ?>

<div id="txt1">
    <h1>Upload fertig.</h1><br />
    <img width="100" height="100" src="<?php echo $this->thumbPath ?>" alt="Hochgeladenes Bild." />
    <label for="url1">Direktlink</label>
    <input type="text" id="url1" onclick="select()" value="<?php echo $this->picPath; ?>" /><br />

    <label for="url2">Bild für Foren</label>
    <input type="text" id="url2" onclick="select()" value="[img]<?php echo $this->picPath; ?>"[/img]" /><br />
    <label for="url3">Miniaturbild mit Link zum Bild für Foren</label>
    <input type="text" id="url3" onclick="select()" value="[url=<?php echo $this->picPath; ?>][img]<?php echo $this->thumbPath ?>[/img][/url]" /><br />
    <label for="url4">Miniaturbild mit Link für eBay™</label>
    <input type="text" id="url4" onclick="select()" value='&lt;a href="<?php echo $this->picPath ?>" target="_blank"&gt;&lt;img src="<?php echo $this->thumbPath; ?>" alt="" /&gt;&lt;/a&gt;' /><br />
    <br />
    <br /><button onclick="location='.'">Zurück</button>
</div>