import numpy as np


def mean( arr ):
    return np.mean(arr)

def var( arr ):
    return np.var(arr)

def get_v_value(values_list) :
    return  var(values_list) / mean(values_list)


def set_weight(obj_list) :
    v_sum = 0
    for item in obj_list:
        v_sum += get_v_value(item.get_value_list())

    for item in obj_list:
        item.set_weight( get_v_value(item.get_value_list()) / v_sum )
