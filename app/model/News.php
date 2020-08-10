<?php

namespace FKSDB\News;

use FKSDB\ORM\Models\ModelContest;
use Nette\DI\Container;
use Nette\SmartObject;

/**
 * Class News
 */
class News {
    use SmartObject;

    private Container $container;

    /**
     * News constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @param ModelContest $contest
     * @param string $lang
     * @return array
     */
    public function getNews(ModelContest $contest, $lang) {
        $contestName = $this->container->getParameters()['contestMapping'][$contest->contest_id];
        if (!isset($this->container->getParameters()[$contestName]['news'][$lang])) {
            return [];
        }
        $news = $this->container->getParameters()[$contestName]['news'][$lang];
        if ($news) {
            return $news;
        } else {
            return [];
        }
    }

}
