<?php

declare(strict_types=1);
class InputField extends BaseField
{
    public string $type;

    public function setType(string $type = '') : self
    {
        $this->type = $type == '' ? self::TYPE_TEXT : $type;
        return $this;
    }

    public function passwordType()
    {
        $this->type = self::TYPE_PASSWORD;

        return $this;
    }

    public function emailType()
    {
        $this->type = self::TYPE_EMAIL;

        return $this;
    }

    public function withLabel() : self
    {
        $this->withLabel = true;

        return $this;
    }

    public function renderField(): string
    {
        return sprintf(
            '<input type="%s" name="%s" value="%s" class="form-control %s %s" id="%s" autocomplete="nope" %s %s>',
            $this->type,
            $this->attribute,
            $this->fieldAttributeValue(),
            isset($this->fieldclass) ? $this->fieldclass . ' ' . $this->attribute : $this->attribute,
            $this->hasErrors(),
            !empty($this->fieldID) ? $this->fieldID : $this->attribute,
            $this->placeholder == '' ? "placeholder=' '" : "placeholder='" . $this->placeholder . "'",
            $this->customAttribute
        );
    }

    public function FieldTemplate(): string
    {
        $template = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'forms' . DS . 'inputfieldTemplate.php');
        $template = str_replace('{{classwrapper}}', $this->FieldwrapperClass ?? '', $template);
        $template = str_replace('{{feedback}}', $this->errors(), $template);
        $template = str_replace('{{labelTemp}}', !empty($this->labelUp) ? $this->labelUp : '%s {{label}}', $template);
        $template = str_replace('{{label}}', $this->withLabel ? $this->fieldLabelTemplate() : '', $template);
        return $template;
    }
}