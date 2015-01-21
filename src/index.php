<?php
require('libs/versionmanager.php');
require('libs/crypto.php');

$v = new version();
$c = new crypto();

require('includes/header.php');

echo '<br><table width="100%" border="0"><tr>
<td width="20%">';

require('includes/navigation.php');

echo '</td><td>';

echo '...';

echo '</td></tr></table><br><center>CryptoManager V '.$v->GetCurrentVersion().'</center>
</body>
</html>';

?>