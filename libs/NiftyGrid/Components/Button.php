<?php
/**
 * NiftyGrid - DataGrid for Nette
 *
 * @author    Jakub Holub
 * @copyright    Copyright (c) 2012 Jakub Holub
 * @license     New BSD Licence
 * @link        http://addons.nette.org/cs/niftygrid
 */

namespace NiftyGrid\Components;

use Nette\Utils\Html;
use NiftyGrid\Grid;

// For constant only

class Button extends \Nette\Application\UI\Component {

    /** @var callback|string */
    private $label;

    /** @var callback|string */
    private $link;

    /** @var callback|string */
    private $text;

    /** @var callback|string */
    private $target;

    /** @var callback|string */
    private $class;

    /** @var bool */
    private $ajax = true;

    /** @var callback|string */
    private $dialog;

    /** @var callback|string */
    private $show = true;

    /**
     * @param string $label
     * @return Button
     */
    public function setLabel($label): self {
        $this->label = $label;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    private function getLabel($row) {
        if (is_callable($this->label)) {
            return call_user_func($this->label, $row);
        }
        return $this->label;
    }

    /**
     * @param callback|string $link
     * @return Button
     */
    public function setLink($link): self {
        $this->link = $link;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    private function getLink($row) {
        if (is_callable($this->link)) {
            return call_user_func($this->link, $row);
        }
        return $this->link;
    }

    /**
     * @param $text
     * @return static
     */
    public function setText($text): self {
        $this->text = $text;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    private function getText($row) {
        if (is_callable($this->text)) {
            return call_user_func($this->text, $row);
        }
        return $this->text;
    }

    /**
     * @param callback|string $target
     * @return Button
     */
    public function setTarget($target): self {
        $this->target = $target;

        return $this;
    }

    /**
     * @param array $row
     * @return callback|mixed|string
     */
    private function getTarget($row) {
        if (is_callable($this->target)) {
            return call_user_func($this->target, $row);
        }
        return $this->target;
    }

    /**
     * @param callback|string $class
     * @return Button
     */
    public function setClass($class): self {
        $this->class = $class;

        return $this;
    }

    /**
     * @param array $row
     * @return callback|mixed|string
     */
    private function getClass($row) {
        if (is_callable($this->class)) {
            return call_user_func($this->class, $row);
        }
        return $this->class;
    }

    public function setAjax(bool $ajax = true): self {
        $this->ajax = $ajax;

        return $this;
    }

    /**
     * @param callback|string $message
     * @return Button
     */
    public function setConfirmationDialog($message) {
        $this->dialog = $message;

        return $this;
    }

    /**
     * @param array $row
     * @return callback|mixed|string
     */
    public function getConfirmationDialog($row) {
        if (is_callable($this->dialog)) {
            return call_user_func($this->dialog, $row);
        }
        return $this->dialog;
    }

    /**
     * @return bool
     */
    private function hasConfirmationDialog() {
        return isset($this->dialog);
    }

    /**
     * @param callback|string $show
     * @return static
     */
    public function setShow($show): self {
        $this->show = $show;

        return $this;
    }

    /**
     * @param array $row
     * @return callback|mixed|string
     */
    public function getShow($row) {
        if (is_callable($this->show)) {
            return (boolean)call_user_func($this->show, $row);
        }
        return $this->show;
    }

    /**
     * @param array $row
     */
    public function render($row): void {
        if (!$this->getShow($row)) {
            return;
        }

        $el = Html::el("a")
            ->href($this->getLink($row))
            ->setText($this->getText($row))
            ->addClass("grid-button")
            ->addClass($this->getClass($row))
            ->setTitle($this->getLabel($row))
            ->setTarget($this->getTarget($row));

        if ($this->getName() == Grid::ROW_FORM) {
            $el->addClass("grid-editable");
        }

        if ($this->hasConfirmationDialog()) {
            $el->addClass("grid-confirm")
                ->addData("grid-confirm", $this->getConfirmationDialog($row));
        }

        if ($this->ajax) {
            $el->addClass("grid-ajax");
        }
        echo $el;
    }

}
