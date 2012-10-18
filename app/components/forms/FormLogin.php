<?php

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class FormLogin extends NAppForm {

    public function __construct(IComponentContainer $parent = NULL, $name = NULL) {
        parent::__construct($parent, $name);

        $this->addText('display_name', 'Jméno')->setDisabled();

        $this->addText('email', 'E-mail')
                ->addRule(NForm::FILLED, 'Vyplňte e-mail.')
                ->addRule(NForm::EMAIL);
    }

}
