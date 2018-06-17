#!/bin/bash

cd /home/dbadmin/dbbak/

gzfile="$(date +'%Y%m%d')01.sql.gz"
sqlfile="$(date +'%Y%m%d').sql"

gunzip -c "$gzfile" > $sqlfile
mysql -uroot -proot -D fcdevdb < $sqlfile
rm $gzfile $sqlfile