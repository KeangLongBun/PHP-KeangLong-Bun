<?php
class Field
{
    private $name;
    private $message;
    private $required;
    private $hasError = FALSE;
    private $value;

    public function __construct($name, $message = '', $required = TRUE)
    {
        $this->name = $name;
        $this->message = $message;
        $this->required = $required;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function hasError()
    {
        return $this->hasError;
    }
    public function isRequired()
    {
        return $this->required;
    }
    public function isEmpty()
    {
        return empty($this->value);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setErrorMessage($message)
    {
        $this->message = $message;
        $this->hasError = TRUE;
    }

    public function clearErrorMessage()
    {
        $this->message = '';
        $this->hasError = FALSE;
    }

    public function getHTML()
    {
        $message = htmlspecialchars($this->message);
        if ($this->hasError()) {
            return '<span class="error">' . $message . '</span>';
        } else {
            return '<span>' . $message . '</span>';
        }
    }
}

class Fields
{
    private $fields = [];

    public function addField($name, $message = '', $required = TRUE)
    {
        $field = new Field($name, $message, $required);
        $this->fields[$name] = $field;
    }

    public function getField($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }
        throw new Exception("Field '$name' does not exist.");
    }

    public function hasErrors()
    {
        foreach ($this->fields as $field) {
            if ($field->hasError()) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
