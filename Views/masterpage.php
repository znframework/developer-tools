<div id="wrapper"><?php

Import::view('top.wizard');

?>
<div id="page-wrapper" style="height:875px;"><?php

Import::view(suffix($page, '.wizard'), $pdata ?? NULL);

?></div></div>
