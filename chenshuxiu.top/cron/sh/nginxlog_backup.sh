#!/bin/bash
## 零点执行该脚本
## 0 0 * * * /bin/bash /home/www/dev/fangcunyisheng.com/cron/sh/nginxlog_backup.sh
## Nginx 日志文件所在的目录
LOGS_PATH=/home/nginxlog/accesslog
## 获取昨天的 yyyy-MM-dd
YESTERDAY=$(date -d "yesterday" +%Y-%m-%d)
## 移动文件
BAK_DIR="${LOGS_PATH}/bak"
if [ ! -d ${BAK_DIR} ]; then
    mkdir ${BAK_DIR}
    chmod 777 ${BAK_DIR}
fi

cd ${BAK_DIR}
if [ -f "../www-fangcunyisheng.com-wx.log" ]; then
    mv ../www-fangcunyisheng.com-wx.log www-fangcunyisheng.com-wx-${YESTERDAY}.log
    tar -zcf www-fangcunyisheng.com-wx-${YESTERDAY}.tar.gz www-fangcunyisheng.com-wx-${YESTERDAY}.log
    rm www-fangcunyisheng.com-wx-${YESTERDAY}.log
fi

if [ -f "../www-fangcunyisheng.com-audit.log" ]; then
    mv ../www-fangcunyisheng.com-audit.log www-fangcunyisheng.com-audit-${YESTERDAY}.log
    tar -zcf www-fangcunyisheng.com-audit-${YESTERDAY}.tar.gz www-fangcunyisheng.com-audit-${YESTERDAY}.log
    rm www-fangcunyisheng.com-audit-${YESTERDAY}.log
fi
cd -
## 向 Nginx 主进程发送 USR1 信号。USR1 信号是重新打开日志文件
kill -USR1 $(cat /usr/local/nginx/logs/nginx.pid)
