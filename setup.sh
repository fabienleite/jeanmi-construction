#!/bin/bash
if [ -f ./web/jeanmi-construction.db ]; then
	rm -rf ./web/jeanmi-construction.db
	echo 'File deleted'
fi

sqlite3 ./web/jeanmi-construction.db < generate-database.sql
chmod 755 ./web/jeanmi-construction.db
echo 'Database created'

echo 'Setted up, see your database :'
ls -l web | grep 'jeanmi-construction.db'