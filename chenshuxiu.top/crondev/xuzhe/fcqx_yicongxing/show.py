# -*- coding: utf-8 -*-

import calcfunctions.calcscores as thisfuncs
import numpy as np
import time

scores = thisfuncs.get_all_patient_last_score()
patient_name = thisfuncs.patient_name
patient_drug = thisfuncs.patient_drug
patient_assess = thisfuncs.patient_assess
patient_lesson = thisfuncs.patient_lesson

#

print(time.strftime( "%Y-%m-%d %H:%M:%S", time.localtime() ))
allnum = len(scores)
print ("总人数 %d" %(allnum))

print (" 权重系数 用药 %0.3f 量表 %0.3f 疗效观察  %0.3f  " %( patient_drug.weight, patient_assess.weight, patient_lesson.weight ))

def printData( scores, title):

    y_dic = { 0:0, 1:0, 2:0, 3:0, 4:0, 5:0, 6:0, 7:0, 8:0, 9:0}
    for data in scores:
        if data[1] < 0.1 :
            y_dic[0] += 1
            continue

        if data[1] < 0.2 :
            y_dic[1] += 1
            continue

        if data[1] < 0.3 :
            y_dic[2] += 1
            continue

        if data[1] < 0.4 :
            y_dic[3] += 1
            continue

        if data[1] < 0.5 :
            y_dic[4] += 1
            continue

        if data[1] < 0.6 :
            y_dic[5] += 1
            continue

        if data[1] < 0.7 :
            y_dic[6] += 1
            continue

        if data[1] < 0.8 :
            y_dic[7] += 1
            continue

        if data[1] < 0.9 :
            y_dic[8] += 1
            continue

        y_dic[9] += 1

    for key in y_dic.iterkeys():
        print("得分%d--%d  %d个人 占全部总人数 %0.3f" %(key*10,(key+1)*10,y_dic[key],float(y_dic[key])/allnum) )


print("总分")
printData( scores, "all")
print("量表")
printData( patient_assess.dic.iteritems(), "assess")
print("用药")
printData( patient_drug.dic.iteritems(), "drug")
print("疗效观察")
printData( patient_lesson.dic.iteritems(), "lesson")
