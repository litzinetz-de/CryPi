<?php

define('TC_BIN','/usr/bin/truecrypt');
define('OVPN_BIN','/usr/sbin/openvpn');
define('PKILL_BIN','/usr/bin/pkill');
define('IPTABLES_BIN','/sbin/iptables');

define('VPN_CONF_BASEDIR','/crypi/');

/////

define('VPN_CONF_ENC',VPN_CONF_BASEDIR.'enc/');
define('VPN_CONF_MNT',VPN_CONF_BASEDIR.'mnt/');
define('BACKENDPATH',VPN_CONF_BASEDIR.'backend/');
define('DATAPATH',VPN_CONF_BASEDIR.'data/');
define('UPLOAD_WORKDIR',VPN_CONF_BASEDIR.'upload_workdir/');
?>
