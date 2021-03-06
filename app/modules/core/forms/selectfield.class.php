<?php

declare(strict_types=1);
class SelectField extends BaseField
{
    public function setType() : self
    {
        $this->type = '';
        return $this;
    }

    public function modelOptions()
    {
        return sprintf(
            '<option value="%s"> %s </option>',
            isset($this->model) ? $this->model->{$this->attribute} : '',
            isset($this->model) ? current($this->model->get_countrie($this->model->{$this->attribute})) ?? '' : ''
        );
    }

    public function options(array $options = []) : self
    {
        $opts = '';
        foreach ($options as $option) {
            $opts .= '<option value="' . $option['id'] . '"> ' . $option['value'] . ' </option>';
        }
        $this->options = $opts;
        return $this;
    }

    public function renderField(): string
    {
        return sprintf(
            '<select name="%s" class="form-select %s %s" id="%s" autocomplete="nope" placeholder=" " %s %s>%s</select>',
            $this->attribute,
            isset($this->fieldclass) ? $this->fieldclass . ' ' . $this->attribute : $this->attribute,
            $this->hasErrors(),
            $this->fieldID ?? $this->attribute,
            $this->customAttribute,
            $this->multiple,
            $this->options
        );
    }

    // public function FieldTemplate(): string
    // {
    //     $template = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'forms' . DS . 'inputfieldTemplate.php');
    //     $template = str_replace('{{classwrapper}}', $this->FieldwrapperClass ?? '', $template);
    //     $template = str_replace('{{feedback}}', $this->errors(), $template);
    //     $template = str_replace('{{labelTemp}}', !empty($this->labelUp) ? $this->labelUp : '%s {{label}}', $template);
    //     $template = str_replace('{{label}}', $this->withLabel ? $this->fieldLabelTemplate() : '', $template);

    //     return $template;
    // }
}