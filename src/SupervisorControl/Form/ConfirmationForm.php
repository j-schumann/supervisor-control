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
     * Creates the CSRF protection and the confirmation button.
     */
    public function prepare()
    {
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

        parent::prepare();
    }
}
