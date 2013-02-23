<?php

/**
 * Formulářový kontejder reprezentující záznam z DB tabulky.
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */ 
abstract class FormContainerModel extends \Nette\Forms\Container {

    public function setDefaults($values, $erase = FALSE) {
        if ($values instanceof Nette\Database\Table\ActiveRow) {
            $values = $values->toArray();
        }
        parent::setDefaults($values, $erase);
    }

}

?>
