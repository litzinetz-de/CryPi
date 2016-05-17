#!/bin/bash

echo "Preparing setup. Will now try to install git, if not already installed."
echo "Git is needed to get a copy of the current stable release of CryptoPi."
echo "Also, I will install all pending updates to your system to make sure it's up to date."
echo "Hit enter to continue."
read

apt-get update
apt-get -y upgrade
apt-get -y install git

echo "Which branch would you like to use?"
echo "Choose master, if you want the latest stable release. If you would like the current development version, choose dev instead."
echo "If you don't know, type master here."
read -p "[master,dev]:" origin_branch

if [ "$origin_branch" != "master" ] && [ "$origin_branch" != "dev" ]
then
	echo "Wrong value. Please start setup again. Will now exit."
	exit 1
fi

echo "cloning/updating github repository"

if [ ! -d crypi_repo/.git ]
then
    git clone -b $origin_branch https://github.com/litzinetz-de/CryPi.git crypi_repo
else
    cd crypi_repo
    git pull origin $origin_branch
    cd ..
fi

echo "Cloning/updating completed. Starting setup..."
chmod u+x crypi_repo/src/setup/setup_backend.sh
./crypi_repo/src/setup/setup_backend.sh
