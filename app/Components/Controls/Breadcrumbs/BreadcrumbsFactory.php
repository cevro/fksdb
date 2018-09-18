<?php

namespace FKSDB\Components\Controls\Breadcrumbs;

use Nette\Application\IRouter;
use Nette\Application\PresenterFactory;
use Nette\Http\Request as HttpRequest;
use Nette\Http\Session;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class BreadcrumbsFactory {

    /** @var Session */
    private $session;

    /**
     * @var IRouter
     */
    private $router;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var PresenterFactory
     */
    private $presenterFactory;

    /**
     * @var string
     */
    private $expiration;

    function __construct($expiration, Session $session, IRouter $router, HttpRequest $httpRequest, PresenterFactory $presenterFactory) {
        $this->expiration = $expiration;
        $this->session = $session;
        $this->router = $router;
        $this->httpRequest = $httpRequest;
        $this->presenterFactory = $presenterFactory;
    }

    /**
     *
     * @return \FKSDB\Components\Controls\Breadcrumbs\Breadcrumbs
     */
    public function create() {
        $component = new Breadcrumbs($this->expiration, $this->session, $this->router, $this->httpRequest, $this->presenterFactory);
        return $component;
    }

}
