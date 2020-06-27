<?php

namespace FKSDB\ORM\Models\StoredQuery;

use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\ModelsMulti\ModelMStoredQueryTag;
use Nette\Database\Table\GroupedSelection;
use Nette\Security\IResource;

/**
 * @todo Better (general) support for related collection setter.
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @property-read string php_post_proc
 * @property-read int query_id
 * @property-read string qid
 * @property-read string sql
 * @property-read string name
 */
class ModelStoredQuery extends AbstractModelSingle implements IResource {

    const RESOURCE_ID = 'storedQuery';

    /**
     * @return ModelStoredQueryParameter[]
     */
    public function getParameters(): array {
        $result = [];
        foreach ($this->related(DbNames::TAB_STORED_QUERY_PARAM, 'query_id') as $row) {
            $result[] = ModelStoredQueryParameter::createFromActiveRow($row);
        }
        return $result;
    }

    public function getTags(): GroupedSelection {
        return $this->related(DbNames::TAB_STORED_QUERY_TAG, 'query_id');
    }

    /**
     * @return ModelMStoredQueryTag[]
     */
    public function getMStoredQueryTags(): array {
        $tags = $this->getTags();

        if (!$tags || count($tags) == 0) {
            return [];
        }
        $result = [];
        /** @var ModelStoredQueryTag $tag */
        foreach ($tags as $tag) {
            $tag->tag_type_id; // stupid touch
            $tagType = $tag->ref(DbNames::TAB_STORED_QUERY_TAG_TYPE, 'tag_type_id');
            $result[] = ModelMStoredQueryTag::createFromExistingModels(
                ModelStoredQueryTagType::createFromActiveRow($tagType), ModelStoredQueryTag::createFromActiveRow($tag)
            );
        }
        return $result;
    }

    public function getResourceId(): string {
        return self::RESOURCE_ID;
    }
}
