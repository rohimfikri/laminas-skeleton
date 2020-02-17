<?php
namespace Core\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class CsrfForm extends Form
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('csrf-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        $inputFilter->add([
            'name'     => 'csrf',
            'required' => true
        ]);
    }
}
