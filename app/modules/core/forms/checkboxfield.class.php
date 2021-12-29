<?php

declare(strict_types=1);
class CheckBoxField extends BaseField
{
    public function setType() : self
    {
        $this->type = self::TYPE_CHECKBOX;
        return $this;
    }

    public function ckecked()
    {
        $this->_ckecked = 'checked';

        return $this;
    }

    public function renderField(): string
    {
        return sprintf(
            '<input type="%s" name="%s" value="%s" class="%s %s" id="%s" %s %s>',
            $this->type,
            $this->attribute,
            'on',
            isset($this->fieldclass) ? 'form-check-input ' . $this->fieldclass : ' form-check-input',
            $this->hasErrors(),
            !empty($this->fieldID) ? $this->fieldID : $this->attribute,
            $this->customAttribute,
            $this->_checked
        );
    }

    public function FieldTemplate(): string
    {
        $template = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'forms' . DS . 'inputcheckboxTemplate.php');
        $template = str_replace('{{wrapperClass}}', $this->FieldwrapperClass ?? '', $template);
        $template = str_replace('{{labelClass}}', $this->labelClass ?? '', $template);
        $template = str_replace('{{inputID}}', !empty($this->fieldID) ? $this->fieldID : $this->attribute, $template);
        $template = str_replace('{{spanClass}}', $this->spanClass ?? '', $template);
        $template = str_replace('{{label}}', $this->label ?? '', $template);

        return $template;
    }

    public function checked() : self
    {
        $this->_checked = 'checked';

        return $this;
    }
}