#!/bin/bash

date=`date +'%Y%m%d%H'`
#echo $file
mysqldump -uroot -pfcqx@20160331 fcqxdb > /home/xdata/dbbak/$date.sql
cd /home/xdata/dbbak 
gzip $date.sql
#mongodump -o /var/ops/backup/mongo$date.old