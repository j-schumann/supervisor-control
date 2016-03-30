<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Form;

use Zend\Form\Form;

/**
 * Primitive form that holds a confirmation button.
 */
class ConfirmationForm extends Form
{
    /**
     * Construct the form and add the fields.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct('supervisor-confirm', $options);

        $this->add([
            'type'    => 'Zend\Form\Element\Csrf',
            'name'    => 'csrfConfirm',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
        ]);

        $this->add([
            'name'       => 'confirm',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Confirm',
            ],
        ]);
    }
}
