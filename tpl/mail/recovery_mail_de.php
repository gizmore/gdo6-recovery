<?php
use GDO\Net\GDT_IP;
/** @var $username string **/
/** @var $link string **/
?>
<div>
Hallo <?=$username?>,<br/>
<br/>
Sie möchten Ihr Passwort auf <?=sitename()?> ändern?<br/>
<br/>
IP der Anfrage: <?=GDT_IP::current()?><br/>
<br/>
Folgen Sie einfach diesem Link oder ignorieren Sie diese Mail.<br/>
<br/>
<?=$link?><br/>
<br/>
Viele Grüße,<br/>
Das <?=sitename()?> Team<br/>
</div>
