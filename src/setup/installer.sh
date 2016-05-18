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
echo "master: Current stable release (choose this if you don't know what the others are)"
echo "testing: Current testing release (latest features and bugfixes, but might have issues)"
echo "dev: Development branch (only use this if you are involved in the development - and only if you know very, very well what you're doing!)"
read -p "[master,testing,dev]:" origin_branch

if [ "$origin_branch" != "master" ] && [ "$origin_branch" != "testing" ] && [ "$origin_branch" != "dev" ]
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
    git fetch origin $origin_branch
    cd ..
fi

echo "Cloning/updating completed. Starting setup..."
chmod u+x crypi_repo/src/setup/setup_backend.sh
./crypi_repo/src/setup/setup_backend.sh
