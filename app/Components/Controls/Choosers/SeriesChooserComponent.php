<?php

namespace FKSDB\Components\Controls\Choosers;

use FKSDB\Models\UI\Title;
use Nette\Application\UI\InvalidLinkException;
use Nette\DI\Container;

class SeriesChooserComponent extends ChooserComponent {

    private int $series;
    private array $allowedSeries;

    public function __construct(Container $container, int $series, array $allowedSeries) {
        parent::__construct($container);
        $this->series = $series;
        $this->allowedSeries = $allowedSeries;
    }

    /* ************ CHOOSER METHODS *************** */
    protected function getTitle(): Title {
        return new Title(sprintf(_('Series %d'), $this->series));
    }

    protected function getItems(): array {
        return $this->allowedSeries;
    }

    /**
     * @param int $item
     */
    public function isItemActive($item): bool {
        return $item === $this->series;
    }

    /**
     * @param int $item
     */
    public function getItemTitle($item): Title {
        return new Title(sprintf(_('Series %d'), $item));
    }

    /**
     * @param int $item
     * @throws InvalidLinkException
     */
    public function getItemLink($item): string {
        return $this->getPresenter()->link('this', ['series' => $item]);
    }
}
