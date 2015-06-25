<?php
/**
 * @copyright   (c) 2014, Vrok
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
    public function __construct($options = array())
    {
        parent::__construct('supervisor-confirm', $options);

        $this->add(array(
            'type'    => 'Zend\Form\Element\Csrf',
            'name'    => 'csrfConfirm',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600,
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'confirm',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Confirm',
            )
        ));
    }
}
