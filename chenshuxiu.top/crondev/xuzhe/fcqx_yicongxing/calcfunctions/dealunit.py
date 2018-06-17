# -*- coding: utf-8 -*-

class Dealunit :
    def __init__( self, origin_dic ):
        self.dic = origin_dic
        self.weight = 0
    def set_weight( self, weight ):
        self.weight = weight

    def get_value_list(self):
        return [ item[1] for item in self.dic.iteritems() ]

class Patient_name(Dealunit) :
    def __init__( self, origin_dic ):
        self.dic = Dealunit( origin_dic ).dic
        self.deal()

    def deal(self):
        self.dic = self.dic

class Patient_drug(Dealunit) :
    def __init__( self, origin_dic ):
        self.dic = Dealunit( origin_dic ).dic
        self.deal()

    def deal(self):
        self.dic = self.dic

class Patient_lesson(Dealunit) :
    def __init__( self, origin_dic ):
        self.dic = Dealunit( origin_dic ).dic
        self.deal()

    def deal(self):
        self.dic = self.dic

class Patient_assess(Dealunit) :
    def __init__( self, origin_dic ):
        self.dic = Dealunit( origin_dic ).dic
        self.deal()

    def deal(self):
        self.dic = self.dic
