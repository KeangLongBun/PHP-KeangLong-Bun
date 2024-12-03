
<?php
class Validate
{
    private $fields;

    public function __construct()
    {
        $this->fields = new Fields();
    }

    public function getFields()
    {
        return $this->fields;
    }

    // Validate a generic text field
    public function text($name, $value, $min = 1, $max = 255)
    {
        $field = $this->fields->getField($name);
        $field->setValue($value);

        if ($field->isRequired() && $field->isEmpty()) {
            $field->setErrorMessage('Required.');
        } else if (strlen($value) < $min && !$field->isEmpty()) {
            $field->setErrorMessage('Too short.');
        } else if (strlen($value) > $max) {
            $field->setErrorMessage('Too long.');
        } else {
            $field->clearErrorMessage();
        }

        return $field;
    }

    // Validate a numeric field
    public function number($name, $value, $min = 0, $max = PHP_FLOAT_MAX)
    {
        $field = $this->fields->getField($name);
        $field->setValue($value);

        if ($field->isRequired() && $field->isEmpty()) {
            $field->setErrorMessage('Required.');
        } else if (!is_numeric($value)) {
            $field->setErrorMessage('Must be a valid number.');
        } else if ($value < $min) {
            $field->setErrorMessage("Must be at least $min.");
        } else if ($value > $max) {
            $field->setErrorMessage("Must be less than or equal to $max.");
        } else {
            $field->clearErrorMessage();
        }

        return $field;
    }

    // Validate a field with a generic pattern
    public function pattern($name, $value, $pattern, $message)
    {
        $field = $this->text($name, $value);

        if (!$field->hasError() && !$field->isEmpty()) {
            $match = preg_match($pattern, $value);
            if ($match === FALSE) {
                $field->setErrorMessage('Error testing field.');
            } else if ($match != 1) {
                $field->setErrorMessage($message);
            } else {
                $field->clearErrorMessage();
            }
        }
    }

    // Validate a phone number
    public function phone($name, $value)
    {
        $field = $this->text($name, $value);

        if (!$field->hasError() && !$field->isEmpty()) {
            $pattern = '/^\d{3}-\d{3}-\d{4}$/';
            $message = 'Invalid phone number format.';
            $this->pattern($name, $value, $pattern, $message);
        }
    }

    // Validate an email address
    public function email($name, $value)
    {
        $field = $this->text($name, $value);

        if (!$field->hasError() && !$field->isEmpty()) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $field->clearErrorMessage();
            } else {
                $field->setErrorMessage('Invalid email address.');
            }
        }
    }
}
?>
