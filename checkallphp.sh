#往主干推点内容，测试钉钉推送
#!/bin/sh
cd ~/dev
rm -f ~/a1.txt

function scandir() {
    local cur_dir parent_dir workdir
    workdir=$1
    cd ${workdir}
    if [ ${workdir} = "/" ]
    then
        cur_dir=""
    else
        cur_dir=$(pwd)
    fi

    for dirlist in $(ls ${cur_dir})
    do
        if test -d ${dirlist};then
            cd ${dirlist}
            scandir ${cur_dir}/${dirlist}
            cd ..
        else
            if [ ${dirlist##*.} == "php" ];then
                php -l ${dirlist} | grep 'No syntax errors detected' >> ~/a1.txt &
            fi
            #echo ${cur_dir}/${dirlist}
        fi
    done
}

scandir fangcunyisheng.com
