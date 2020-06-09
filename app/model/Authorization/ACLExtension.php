<?php

namespace Authorization;

use Authorization\Assertions\QIDAssertion;
use Authorization\Assertions\StoredQueryTagAssertion;
use FKSDB\Config\Expressions\Helpers;
use Nette\DI\CompilerExtension;
use Nette\Security\Permission;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ACLExtension extends CompilerExtension {
    /** @var string[] */

    public static $semanticMap = [
        'qid' => QIDAssertion::class,
        'queryTag' => StoredQueryTagAssertion::class,
    ];

    public function __construct() {
        Helpers::registerSemantic(self::$semanticMap);
    }

    public function loadConfiguration() {
        parent::loadConfiguration();

        $builder = $this->getContainerBuilder();
        $definition = $builder->addDefinition('authorization')
            ->setFactory(Permission::class);

        $config = $this->getConfig();

        foreach ($config as $setup) {
            $stmt = Helpers::statementFromExpression($setup);
            $definition->addSetup($stmt->entity, $stmt->arguments);
        }
    }
}
