<?php

declare(strict_types=1);
class TextareaField extends BaseField
{
    protected int $row;

    public function setType() : self
    {
        $this->type = '';
        return $this;
    }

    public function row(int $r)
    {
        $this->row = $r;

        return $this;
    }

    public function renderField(): string
    {
        return sprintf(
            '<textarea name="%s" class="form-control %s %s" id="%s" row="%s" autocomplete="nope" %s %s aria-describedby=%s>%s</textarea>',
            $this->attribute,
            $this->fieldclass ?? '',
            $this->hasErrors(),
            $this->fieldID ?? $this->attribute,
            $this->row ?? '',
            $this->placeholder == '' ? "placeholder=' '" : "placeholder='" . $this->placeholder . "'",
            $this->customAttribute,
            $this->attribute . '-feedback',
            $this->fieldAttributeValue(),
        );
    }
}