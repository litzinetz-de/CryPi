#!/bin/bash

echo "Preparing setup. Will now try to install git, if not already installed."
echo "Git is needed to get a copy of the current stable release of CryptoPi."
echo "Also, I will install all pending updates to your system to make sure it's up to date."
echo "Hit enter to continue."
read

apt-get update
apt-get upgrade
apt-get -y install git

echo "cloning/updating github repository"

if [ ! -d crypi_repo/.git ]
then
    git clone https://github.com/litzinetz-de/CryPi.git crypi_repo
else
    cd crypi_repo
    git pull origin master
    cd ..
fi

echo "Cloning/updating completed. Starting setup..."
chmod u+x crypi_repo/src/setup/setup_backend.sh
./crypi_repo/src/setup/setup_backend.sh