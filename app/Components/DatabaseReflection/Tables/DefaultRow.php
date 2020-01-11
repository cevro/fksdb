<?php

namespace FKSDB\Components\DatabaseReflection;

use FKSDB\Components\DatabaseReflection\ValuePrinters\StringPrinter;
use FKSDB\ORM\AbstractModelSingle;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;

/**
 * Class DefaultRow
 * @package FKSDB\Components\DatabaseReflection
 */
abstract class DefaultRow extends AbstractRow {
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var string
     */
    private $modelAccessKey;
    /**
     * @var string
     */
    private $description;
    /**
     * @var array
     */
    private $metaData;
    /**
     * @var int
     */
    private $permissionValue = self::PERMISSION_USE_GLOBAL_ACL;
    /**
     * @var MetaDataFactory
     */
    private $metaDataFactory;

    /**
     * StringRow constructor.
     * @param ITranslator $translator
     * @param MetaDataFactory $metaDataFactory
     */
    public function __construct(ITranslator $translator, MetaDataFactory $metaDataFactory) {
        $this->metaDataFactory = $metaDataFactory;
        parent::__construct($translator);
    }

    /**
     * @param string $tableName
     * @param string $title
     * @param string $modelAccessKey
     * @param array $metaData
     * @param string|null $description
     */
    public final function setUp(string $tableName, string $modelAccessKey, array $metaData, string $title, string $description = null) {
        $this->title = $title;
        $this->tableName = $tableName;
        $this->modelAccessKey = $modelAccessKey;
        $this->description = $description;
        $this->metaData = $this->metaDataFactory->getMetaData($tableName, $modelAccessKey);
    }

    /**
     * @param $value
     */
    public final function setPermissionValue(string $value) {
        $this->permissionValue = constant(self::class . '::' . $value);
    }

    /**
     * @inheritDoc
     */
    public final function getPermissionsValue(): int {
        return $this->permissionValue;
    }

    /**
     * @inheritDoc
     */
    public final function getTitle(): string {
        return _($this->title);
    }

    /**
     * @return string|null
     */
    public final function getDescription() {
        return $this->description ? _($this->description) : '';
    }

    /**
     * @return string
     */
    protected final function getModelAccessKey(): string {
        return $this->modelAccessKey;
    }

    /**
     * @return string
     */
    protected final function getTableName(): string {
        return $this->tableName;
    }
}
