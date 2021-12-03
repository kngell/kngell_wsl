<?php

declare(strict_types=1);
class Minvalidator extends CustomValidator
{
    public function runValidation()
    {
        $value = $this->_model->{$this->field};
        $pass = (strlen($value) >= $this->rule);

        return $pass;
    }
}
