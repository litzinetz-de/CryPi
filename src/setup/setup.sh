#!/bin/bash

# CryPi installer

fresh_install_generic()
{
  echo -e "\033[0;33mI will now install some software and upgrade the system."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  apt-get update
  apt-get upgrade
  apt-get install apache2 git
  wwwdir=$(apachectl -S 2> /dev/null | grep "Main DocumentRoot" | sed -e 's/\<Main DocumentRoot\>//g' | sed s/://g | sed 's/ //g')
  echo -e "\033[0;33mDone. I will now start installing CryptoPi."
  echo -e "Hit enter to continue.\033[0;37m"
  read
  rm -rf $wwwdir/*
  mkdir crypi_repo
  git clone https://github.com/litzinetz-de/CryPi.git crypi_repo
  cp -R crypi_repo/src/frontend/* $wwwdir
  chown -R www-data:www-data $wwwdir
  cp crypi_repo/src/init-scripts/crypi_init /etc/init.d/
  chmod ugo+x /etc/init.d/crypi_init
  update-rc.d crypi_init defaults
  mkdir /crypi
  mkdir /crypi/data
  mkdir /crypi/enc
  mkdir /crypi/mnt
  mkdir /crypi/scripts
  mkdir /crypi/upload_tmp
  mkdir /crypi/upload_workdir
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

