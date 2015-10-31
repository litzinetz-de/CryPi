#!/bin/sh
### BEGIN INIT INFO
# Provides:          crypi_ramdisk
# Required-Start:    $local_fs $network $named $time $syslog
# Required-Stop:     $local_fs $network $named $time $syslog
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Description:       Sets IPv4 addresses like configured in CryPi settings
### END INIT INFO
 
start() {
  mount -t tmpfs -o size=25M none /crypi/upload_tmp
}
 
stop() {
  umount /crypi/upload_tmp
}
 
case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  retart)
    stop
    start
    ;;
  *)
    echo "Usage: $0 {start|stop|restart}"
esac
