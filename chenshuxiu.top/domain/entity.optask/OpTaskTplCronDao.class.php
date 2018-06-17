<?php
    /*
     * OpTaskTplCronDao
     */
    class OpTaskTplCronDao extends Dao {
        public static function getByOptasktplidStep ($optasktplid, $step) {
            $cond = " and optasktplid = :optasktplid and step = :step ";
            $bind = [
                ':optasktplid' => $optasktplid,
                'step' => $step
            ];

            return Dao::getEntityByCond('OpTaskTplCron', $cond, $bind);
        }
    }