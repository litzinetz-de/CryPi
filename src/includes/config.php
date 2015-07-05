<?php
define('MYSQL_HOST','localhost');
define('MYSQL_USER','local');
define('MYSQL_PWD','local');
define('MYSQL_DB','crypto');

define('TC_BIN','/usr/bin/truecrypt');
define('OVPN_BIN','/usr/sbin/openvpn');
define('PKILL_BIN','/usr/bin/pkill');

define('VPN_CONF_BASEDIR','/crypi/');
define('CRYPI_LOGFILE','/var/log/crypi.log');

/////

define('VPN_CONF_ENC',VPN_CONF_BASEDIR.'enc/');
define('VPN_CONF_MNT',VPN_CONF_BASEDIR.'mnt/');
define('OVPN_PIDFILE',VPN_CONF_BASEDIR.'ovpn.pid');
?>
