<?php

declare(strict_types=1);
abstract class BaseField implements FieldInterface
{
    protected const TYPE_TEXT = 'text';
    protected const TYPE_EMAIL = 'email';
    protected const TYPE_NUMBER = 'number';
    protected const TYPE_PASSWORD = 'password';
    protected const TYPE_CHECKBOX = 'checkbox';
    protected const TYPE_RADIO = 'radio';
    protected const TYPE_HIDDEN = 'hidden';
    protected const Type_BUTTON = 'button';
    protected string $attribute = '';
    protected string $label;
    protected string $FieldwrapperClass = 'mb-3';
    protected string $labelClass;
    protected string $require = '';
    protected string $fieldclass;
    protected string $fieldID;
    protected string $customAttribute = '';
    protected string $spanClass;
    protected string $fieldValue = '';
    protected string $labelUp;
    protected bool $withLabel = false;
    protected bool $withWrapper = true;
    protected string $labelDescr;
    protected string $labelTextClass;
    protected string $tagText = '';
    protected string $title = '';
    protected string $placeholder = '';
    protected string $helpBlock = '';
    protected string $options = '';
    protected string $_checked = '';
    protected string $icon = '';
    protected string $multiple = '';

    protected Model $model;

    public function __toString() : string
    {
        return sprintf(
            $this->FieldTemplate(),
            $this->renderField(),
        );
    }

    public function hidden() : self
    {
        $this->type = self::TYPE_HIDDEN;
        return $this;
    }

    public function checkboxType()
    {
        $this->type = self::TYPE_CHECKBOX;

        return $this;
    }

    // public function setFieldWrapper(string $fieldWrapper)
    // {
    //     $this->fieldWrapper = $fieldWrapper;
    // }

    public function setAttr(string $attribute) : self
    {
        $this->type = self::TYPE_TEXT;
        $this->attribute = $attribute;

        return $this;
    }

    public function setClass(array $args = []) : self
    {
        foreach ($args as $tag => $class) {
            $this->$tag = $class;
        }

        return $this;
    }

    public function noWrapper() : self
    {
        $this->withWrapper = false;
        return $this;
    }

    public function setModel(?Model $model = null) : self
    {
        if (null == $model) {
            if (isset($this->model)) {
                unset($this->model);
            }
        } else {
            $this->model = $model;
        }

        return $this;
    }

    public function submit()
    {
        $this->type = 'submit';
        return $this;
    }

    public function setDefault() : self
    {
        foreach ($this as $key => $value) {
            if ($key == 'FieldwrapperClass') {
                $this->{$key} = 'mb-3';
            } elseif (is_string($value)) {
                $this->{$key} = '';
            } else {
                if (is_bool($value)) {
                    $this->{$key} = false;
                }
            }
        }
        return $this;
    }

    public function text(string $text)
    {
        $this->tagText = $text;
        return $this;
    }

    public function icon(string $iconClass) : self
    {
        $ic = '<i class= "';
        $ic .= $iconClass;
        $ic .= '"></i>';
        $this->icon = $ic;
        return $this;
    }

    public function title(string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    public function placeholder(string $placeholder) : self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function textClass(string $class) : self
    {
        $this->labelTextClass = $class;

        return $this;
    }

    public function spanClass(string $class)
    {
        $this->spanClass = $class;

        return $this;
    }

    public function attr(array $attrs = [])
    {
        foreach ($attrs as $key => $attr) {
            $this->customAttribute .= $key . ' = "' . $attr . '" ';
        }

        return $this;
    }

    public function multiple() : self
    {
        $this->multiple = 'multiple aria-label="multiple"';
        return $this;
    }

    public function nestField(string $formID, bool $nestField) : self
    {
        $this->customAttribute = $nestField ? 'form ' . ' = ' . $formID : '';

        return $this;
    }

    public function class(string $custom)
    {
        $actual = $this->fieldclass ?? '';
        $separator = empty($this->fieldclass) ? '' : ' ';
        $this->fieldclass = $actual . $separator . $custom;
        return $this;
    }

    public function id(string $id)
    {
        $this->fieldID = $id;

        return $this;
    }

    public function Label(string $label) : self
    {
        $this->withLabel = true;
        $this->label = $label;

        return $this;
    }

    public function labelDescr(string $descr) : self
    {
        $this->labelDescr = $descr;

        return $this;
    }

    public function labelUp(string $label)
    {
        $this->withLabel = true;
        $this->label = $label;
        $this->labelUp = ' {{label}} %s';

        return $this;
    }

    public function LabelClass(string $labelclass) : self
    {
        $this->labelClass = $labelclass;

        return $this;
    }

    public function removeWrapperClass(string $class) : self
    {
        $this->FieldwrapperClass = str_replace($class, '', $this->FieldwrapperClass);
        return $this;
    }

    public function setFieldWrapperClass(?string $wrapper) : self
    {
        $this->FieldwrapperClass .= ' ' . $wrapper;
        return $this;
    }

    public function helpBlock(string $str) : self
    {
        $span = ' <span class="help-block"><small>';
        $span .= $str;
        $span .= '</small></span>';
        $this->helpBlock = $span;
        return $this;
    }

    public function required()
    {
        $this->require = '<span class="text-danger">*</span>';

        return $this;
    }

    public function value(string $val) : self
    {
        $this->fieldValue = $val;

        return $this;
    }

    public function fieldID()
    {
        if (isset($this->model)) {
            return $this->fieldID . $this->model->{$this->model->get_colID()};
        }

        return $this->fieldID;
    }

    public function labelValue()
    {
        if (isset($this->model) && !isset($this->label)) {
            return $this->model->htmlDecode($this->model->{$this->attribute}) ?? '';
        }

        return $this->label;
    }

    public function feedbackField() : string
    {
        return '<div class="invalid-feedback">{{feedback}}</div>';
    }

    public function labelDescrValue()
    {
        if (isset($this->model)) {
            return $this->model->htmlDecode($this->labelDescr) ?? '';
        }

        return $this->labelDescr;
    }

    public function fieldValue()
    {
        if (isset($this->model) && (!isset($this->fieldValue) || empty($this->fieldValue))) {
            return $this->model->htmlDecode((string) $this->model->{$this->model->get_colID()});
        }

        return $this->fieldValue ?? '';
    }

    public function fieldAttributeValue() :string
    {
        if (isset($this->model)) {
            return $this->model->htmlDecode($this->model->{$this->attribute});
        }

        return $this->fieldValue;
    }

    public function hasErrors()
    {
        if (isset($this->model)) {
            return $this->model->hasError($this->attribute) ? 'is-invalid' : '';
        }

        return '';
    }

    public function errors() : string
    {
        if (isset($this->model)) {
            return (string) $this->model->getFirstError($this->attribute);
        }

        return '';
    }

    public function fieldLabelTemplate() : string
    {
        $template = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'forms' . DS . 'inputLabelTemplate.php');
        $template = str_replace('{{inputID}}', $this->fieldID ?? $this->attribute, $template);
        $template = str_replace('{{classlabel}}', $this->labelClass ?? '', $template);
        $template = str_replace('{{label}}', $this->label ?? '', $template);
        $template = str_replace('{{req}}', ($this->require != '') ? $this->require : '', $template);

        return $template;
    }

    public function FieldTemplate(): string
    {
        $template = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'forms' . DS . 'inputfieldTemplate.php');
        $template = str_replace('{{classwrapper}}', $this->FieldwrapperClass ?? '', $template);
        $template = str_replace('{{feedbackField}}', $this->require != '' ? $this->feedbackField() : '', $template);
        $template = str_replace('{{feedback}}', $this->errors(), $template);
        $template = str_replace('{{labelTemp}}', !empty($this->labelUp) ? $this->labelUp : '%s {{label}}', $template);
        $template = str_replace('{{label}}', $this->withLabel ? $this->fieldLabelTemplate() : '', $template);
        $template = str_replace('{{helpBlock}}', $this->helpBlock != '' ? $this->helpBlock : '', $template);
        return $template;
    }
}