#!/bin/bash

file=`date +'%Y%m%d01.sql.gz'`
echo $file
cd /home/dbadmin/dbbak/
scp 123.56.89.157:/home/xdata/dbbak/$file /home/dbadmin/dbbak/