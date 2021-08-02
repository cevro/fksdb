<?php

declare(strict_types=1);

namespace FKSDB\Models\Authorization\Assertions;

use FKSDB\Models\StoredQuery\StoredQuery;
use Nette\InvalidArgumentException;
use Nette\Security\Resource;
use Nette\Security\Role;
use Nette\Security\Permission;
use Nette\SmartObject;

class StoredQueryTagAssertion
{
    use SmartObject;

    private array $tagNames;

    /**
     * StoredQueryTagAssertion constructor.
     * @param array|string $tagNames
     */
    public function __construct($tagNames)
    {
        if (!is_array($tagNames)) {
            $tagNames = [$tagNames];
        }
        $this->tagNames = $tagNames;
    }

    /**
     * @param Permission $acl
     * @param Role $role
     * @param Resource|string|null $resourceId
     * @param string|null $privilege
     * @return bool
     */
    public function __invoke(Permission $acl, $role, $resourceId, $privilege): bool
    {
        $storedQuery = $acl->getQueriedResource();
        if (!$storedQuery instanceof StoredQuery) {
            throw new InvalidArgumentException('Expected StoredQuery, got \'' . get_class($storedQuery) . '\'.');
        }
        foreach ($storedQuery->getQueryPattern()->getStoredQueryTagTypes() as $tagType) {
            if (in_array($tagType->name, $this->tagNames)) {
                return true;
            }
        }
        return false;
    }
}
