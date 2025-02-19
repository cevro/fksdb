<?php

namespace FKSDB\Models\Exports;

use FKSDB\Models\Exceptions\GoneException;
use FKSDB\Models\Exports\Formats\CSVFormat;
use FKSDB\Models\ORM\Services\ServiceContest;
use FKSDB\Models\ORM\Services\ServiceEvent;
use FKSDB\Models\StoredQuery\StoredQuery;
use FKSDB\Models\StoredQuery\StoredQueryFactory;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Nette\SmartObject;

class ExportFormatFactory {

    use SmartObject;

    /** @deprecated */
    public const AESOP = 'aesop';
    public const CSV_HEADLESS = 'csv';
    public const CSV_HEAD = 'csvh';
    public const CSV_QUOTE_HEAD = 'csvqh';

    private Container $container;

    private StoredQueryFactory $storedQueryFactory;

    private ServiceEvent $serviceEvent;

    private ServiceContest $serviceContest;

    public array $defaultFormats;

    public function __construct(Container $container, StoredQueryFactory $storedQueryFactory, ServiceEvent $serviceEvent, ServiceContest $serviceContest) {
        $this->container = $container;
        $this->storedQueryFactory = $storedQueryFactory;
        $this->serviceEvent = $serviceEvent;
        $this->serviceContest = $serviceContest;
        $this->defaultFormats = [
            self::CSV_HEAD => _('Save CSV'),
            self::CSV_HEADLESS => _('Save CSV (without head)'),
            self::CSV_QUOTE_HEAD => _('Save CSV with quotes'),
        ];
    }

    /**
     * @throws GoneException
     */
    public function createFormat(string $name, StoredQuery $storedQuery): ExportFormat {
        switch (strtolower($name)) {
            case self::AESOP:
                throw new GoneException();
            case self::CSV_HEADLESS:
                return $this->createCSV($storedQuery, false);
            case self::CSV_HEAD:
                return $this->createCSV($storedQuery, true);
            case self::CSV_QUOTE_HEAD:
                return $this->createCSV($storedQuery, true, true);
            default:
                throw new InvalidArgumentException('Unknown format \'' . $name . '\'.');
        }
    }

    private function createCSV(StoredQuery $storedQuery, bool $header, bool $quote = CSVFormat::DEFAULT_QUOTE): CSVFormat {
        return new CSVFormat($storedQuery, $header, CSVFormat::DEFAULT_DELIMITER, $quote);
    }
}
