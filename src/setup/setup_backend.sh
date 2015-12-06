#!/bin/bash

# CryPi installer

fresh_install_generic()
{
  echo -e "\033[0;33mI will now install some software and upgrade the system."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  apt-get -y install apache2 php5 perl sudo openvpn
  wwwdir=$(apachectl -S 2> /dev/null | grep "Main DocumentRoot" | sed -e 's/\<Main DocumentRoot\>//g' | sed s/://g | sed 's/ //g' | sed 's/\"//g')
  echo -e "\033[0;33mDone. I will now start installing CryptoPi."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  echo "Clearing up wwwdir ($wwwdir)"
  rm -rf $wwwdir/*
  echo "Installing web frontend"
  cp -R crypi_repo/src/frontend/* $wwwdir
  chown -R www-data:www-data $wwwdir
  echo "Installing init scripts"
  cp crypi_repo/src/init-scripts/crypi_init /etc/init.d/
  chmod ugo+x /etc/init.d/crypi_init
  update-rc.d crypi_init defaults
  echo "Creating crypi directories"
  mkdir /crypi 2> /dev/null
  mkdir /crypi/data 2> /dev/null
  mkdir /crypi/enc 2> /dev/null
  mkdir /crypi/mnt 2> /dev/null
  mkdir /crypi/scripts 2> /dev/null
  mkdir /crypi/upload_tmp 2> /dev/null
  mkdir /crypi/upload_workdir 2> /dev/null
  echo "Installing crypi scripts"
  cp crypi_repo/src/scripts/* /crypi/scripts
  chown -R www-data:www-data /crypi
  chmod u+x /crypi/scripts/*
  echo "Creating default config files"
  echo "false" > /crypi/data/bypass_vpn.dat
  echo "192.168.0.254" > /crypi/data/networking_addr.dat
  echo "255.255.255.0" > /crypi/data/networking_mask.dat
  echo "192.168.0.1" > /crypi/data/networking_gateway.dat
  
  echo -e "\033[0;33mDone. I will now modify PHP's an the system's config files to fit our needs."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  perl -p -i.bak -e 's/;upload_tmp_dir =/upload_tmp_dir = \/crypi\/upload_tmp\/ /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/post_max_size = 8M/post_max_size = 20M /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/#net.ipv4.ip_forward=1/net.ipv4.ip_forward=1 /' /etc/sysctl.conf
}

########################

echo -e "\033[1;31m=============="
echo -e "\033[1;31mCryptoPi setup"
echo -e "\033[1;31m=============="
echo
echo -e "\033[0;37mThis tool will help you to install or upgrade CryptoPi."
echo
echo -e "\033[0;33mIs this a fresh installation (i) or an upgrade (u)?\033[0;37m"
#echo -e "\033[0;37m"
read -p "[i,u]:" install_type

if [ "$install_type" = "i" ]
then
  fresh_install_generic
elif [ "$install_type" = "u" ]
then
  echo "Upgrade"
else
  echo "Wrong value. Please start setup again. Will now exit."
  exit 1
fi

