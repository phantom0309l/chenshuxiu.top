<?php

class ObjectArray
{

    private $data;

    public function __construct ($data) {
        $this->data = $data;
    }

    public function sort ($field, $desc = false) {
        if ($desc)
            $compare = '<';
        else
            $compare = '>';
        usort($this->data,
                create_function('$o1, $o2', "if (\$o1->$field() == \$o2->$field()) return 0; return (\$o1->$field() $compare \$o2->$field()) ? -1 : 1;"));
    }

    public function filterEqual ($field, $value) {
        $this->filter('==', $field, $value);
    }

    public function filterNotEqual ($field, $value) {
        $this->filter('!=', $field, $value);
    }

    public function filterInclude ($field, $value) {
        $this->data = array_filter($this->data, create_function('$object', "return '' != stristr(\$object->$field(), '$value');"));
    }

    private function filter ($op, $field, $value) {
        $this->data = array_filter($this->data, create_function('$object', "return \$object->$field() $op $value;"));
    }

    public function getArray () {
        return $this->data;
    }
}
?>