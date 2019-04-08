#!/bin/bash

yes | apt install wireshark sudo ssh apache2 sqlite3 php7.0 php-sqlite3

if [ $? -eq 0 ]; then
	echo 'Packets succesfully installed'
else
	echo 'Packets installation failed'
	exit 1
fi

if [ -f ./web/jeanmi-construction.db ]; then
	rm -rf ./web/jeanmi-construction.db
	echo 'File deleted'
fi

sqlite3 ./web/jeanmi-construction.db < generate-database.sql
chmod 755 ./web/jeanmi-construction.db
echo 'Database created'

useradd jeanmi -m
if [ $? -eq 0 ]; then
	echo 'User jeanmi succesfully created'
else
	echo 'Failed to create user jeanmi'
	exit 2
fi

cp -R ./web/* /var/www/
echo 'Web files moved to /var/www'

chown jeanmi:jeanmi /var/www/*

cp confs/sudoers /etc/sudoers
cp confs/init.lua /etc/wireshark/init.lua
cp confs/apache2.conf /etc/apache2/apache2.conf
cp confs/000-default.conf /etc/apache2/sites-available/000-default.conf
cp confs/envvars /etc/apache2/envvars
cp confs/sshd_config /etc/ssh/sshd_config
echo 'All configurations files are setted up'

rm /root/.XAuthority
ln -s /home/jeanmi/.Xauthority /root/.Xauthority

systemctl daemon-reload
systemctl restart ssh
systemctl enable ssh
systemctl restart apache2
systemctl enable apache2

echo "ENSIBS{part3_c0mM4nD_Ex3cu71on_i5_DanGErOuS}" > /home/jeanmi/flag
chown jeanmi:jeanmi /home/jeanmi/flag

echo "ENSIBS{part4_d0n7_usE_SudO_bUt_GroUPs}" > /root/flag
exit 0
