<?php
require('libs/crypto.php');
$c = new crypto();

//if($c->create_container('asdf 123','test.tc'))
//if($c->mount_container('asdf 123','test.tc'))
//if($c->dismount_container())
//if($c->container_mounted())
/*{
	echo 'ok';
} else {
	echo 'error';
}*/

//$buffer=$c->ReadConfigs('/crypi/mnt/');

$buffer=$c->FindConfigs();

print_r($buffer);

?>