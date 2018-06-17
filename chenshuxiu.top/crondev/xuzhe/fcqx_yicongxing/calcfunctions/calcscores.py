# -*- coding: utf-8 -*-

import dealunit
import tools

# patient_name
import patient_name_data
patient_name = dealunit.Patient_name(patient_name_data.patient_name_data)

# patient_drug
import patient_drug_data
patient_drug = dealunit.Patient_drug(patient_drug_data.patient_drug_data)

# patient_assess
import patient_assess_data
patient_assess = dealunit.Patient_assess(patient_assess_data.patient_assess_data)

# patient_lesson
import patient_lesson_data
patient_lesson = dealunit.Patient_lesson(patient_lesson_data.patient_lesson_data)

def set_weight():
    obj_list = [\
    patient_drug,\
    patient_assess,\
    patient_lesson\
    ]

    return tools.set_weight(obj_list)

def get_all_patient_last_score():
    set_weight()
    scores_dic = {}

    for k in patient_name.dic.keys():
        scores_dic[k] = patient_drug.weight * patient_drug.dic[k] \
        +  patient_assess.weight * patient_assess.dic[k]  \
        +  patient_lesson.weight * patient_lesson.dic[k]

    return sorted(scores_dic.iteritems(), key=lambda d:d[1], reverse = False )
