#!/bin/bash

# CryPi installer

######################## Internal functions

init_patchlist()
{
	touch /crypi/patchlist.dat
	chmod ugo+r /crypi/patchlist.dat	
}

is_patched()
{
	if grep -Fxq "$1" /crypi/patchlist.dat
	then
		return 0
	else
		return 1
	fi
}

set_patched()
{
	echo $1 >> /crypi/patchlist.dat
}

######################## Patches

#patch_skel_0-0-0()
#{
#	echo -e "Installing patch skel_0-0-0..."
#	if is_patched p_skel_0-0-0 ;
#	then
#		echo -e "Already patched."
#	else
#		set_patched p_skel_0-0-0
#		# ...
#	fi
#}

######################## Install and update functions

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
  init_patchlist
  echo "Installing crypi scripts"
  cp crypi_repo/src/scripts/* /crypi/scripts
  chown -R www-data:www-data /crypi
  chmod u+x /crypi/scripts/*
  echo "Creating default config files"
  echo "false" > /crypi/data/bypass_vpn.dat
  echo "192.168.0.254" > /crypi/data/networking_addr.dat
  echo "255.255.255.0" > /crypi/data/networking_mask.dat
  echo "192.168.0.1" > /crypi/data/networking_gateway.dat
  chown -R www-data:www-data /crypi/
  
  echo -e "\033[0;33mDone. I will now modify PHP's an the system's config files to fit our needs."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  perl -p -i.bak -e 's/;upload_tmp_dir =/upload_tmp_dir = \/crypi\/upload_tmp\/ /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/post_max_size = 8M/post_max_size = 20M /' /etc/php5/apache2/php.ini
  perl -p -i.bak -e 's/#net.ipv4.ip_forward=1/net.ipv4.ip_forward=1 /' /etc/sysctl.conf
  
  grep "www-data ALL = NOPASSWD: /usr/bin/truecrypt" /etc/sudoers > /dev/null
  if [ $? -eq 1 ]
  then
    echo "www-data ALL = NOPASSWD: /usr/bin/truecrypt" >> /etc/sudoers
  fi
  grep "www-data ALL = NOPASSWD: /sbin/ifconfig" /etc/sudoers > /dev/null
  if [ $? -eq 1 ]
  then
    echo "www-data ALL = NOPASSWD: /sbin/ifconfig" >> /etc/sudoers
  fi
  grep "www-data ALL = NOPASSWD: /sbin/route" /etc/sudoers > /dev/null
  if [ $? -eq 1 ]
  then
    echo "www-data ALL = NOPASSWD: /sbin/route" >> /etc/sudoers
  fi
  grep "www-data ALL = NOPASSWD: /usr/sbin/openvpn" /etc/sudoers > /dev/null
  if [ $? -eq 1 ]
  then
    echo "www-data ALL = NOPASSWD: /usr/sbin/openvpn" >> /etc/sudoers
  fi
  grep "www-data ALL = NOPASSWD: /usr/bin/pkill openvpn" /etc/sudoers > /dev/null
  if [ $? -eq 1 ]
  then
    echo "www-data ALL = NOPASSWD: /usr/bin/pkill openvpn" >> /etc/sudoers
  fi
  
  /etc/init.d/sudo restart
  
  echo -e "\033[0;33mOn which platform are we working now? Please choose between:"
  echo -e "Rapberry Pi (rpi), x86 or x64:\033[0;37m"
  
  read -p "[rpi,x86,x64]:" platform_type
  
  if [ "$platform_type" = "rpi" ]
  then
    cp crypi_repo/bin/rpi/truecrypt /usr/bin/
    chmod ugo+x /usr/bin/truecrypt
  elif [ "$platform_type" = "x86" ]
  then
    cp crypi_repo/bin/x86/truecrypt /usr/bin/
    chmod ugo+x /usr/bin/truecrypt
  elif [ "$platform_type" = "x64" ]
  then
    cp crypi_repo/bin/x64/truecrypt /usr/bin/
    chmod ugo+x /usr/bin/truecrypt
  else
    echo "Wrong value. Please start setup again. Will now exit."
    exit 1
  fi

  echo -e "\033[1;31m====================="
  echo -e "\033[1;31mInstallation finished"
  echo -e "\033[1;31m====================="
  echo
  echo -e "\033[0;37mTo complete installation, please reboot the system now." 
  echo
  echo -e "\033[0;31mPLEASE NOTE: After rebooting, the device will have an IP address of 192.168.0.254" 
  echo -e "\033[0;37mYou can change the IP address using the web interface, either now (current IP) or after rebooting (IP above)." 
  echo
  echo -e "Hope, you will enjoy CryptoPi. Your feedback is appreciated! info@litzinetz.de\033[0;37m"
}

update_generic()
{
	echo -e "\033[0;33mI will now update CryptoPi to the current version of the chosen branch."
	echo -e "Hit enter to continue.\033[0;37m"
	read
	wwwdir=$(apachectl -S 2> /dev/null | grep "Main DocumentRoot" | sed -e 's/\<Main DocumentRoot\>//g' | sed s/://g | sed 's/ //g' | sed 's/\"//g')
	echo "Updating web frontend"
	cp -R crypi_repo/src/frontend/* $wwwdir
	chown -R www-data:www-data $wwwdir
	echo "Updating init scripts"
	cp crypi_repo/src/init-scripts/crypi_init /etc/init.d/
	echo "Updating crypi scripts"
	cp crypi_repo/src/scripts/* /crypi/scripts
	chown -R www-data:www-data /crypi
	chmod u+x /crypi/scripts/*
	
	init_patchlist
	
	echo -e "\033[1;31m==============="
	echo -e "\033[1;31mUpdate finished"
	echo -e "\033[1;31m==============="
	echo
	echo -e "\033[0;37mYour system is up to date now. A reboot is not required." 
	echo -e "Hope, you will enjoy CryptoPi. Your feedback is appreciated! info@litzinetz.de\033[0;37m"
}

######################## Main functions

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
  update_generic
else
  echo "Wrong value. Please start setup again. Will now exit."
  exit 1
fi

