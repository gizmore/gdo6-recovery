<?php
use GDO\Net\GDT_IP;
/** @var $username string **/
/** @var $link string **/
?>
<div>
Dear <?=$username?>,<br/>
<br/>
You want to change your password on <?=sitename()?>?<br/>
<br/>
Requesting IP: <?=GDT_IP::current()?><br/>
<br/>
Simply follow this link, or ignore this mail.<br/>
<br/>
<?=$link?><br/>
<br/>
Kind Regards,
The <?=sitename()?> Team
</div>
