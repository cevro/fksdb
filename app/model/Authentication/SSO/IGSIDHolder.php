<?php

namespace FKSDB\Authentication\SSO;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
interface IGSIDHolder {

    public function getGSID();

    /**
     * @param mixed $gsid
     * @return void
     */
    public function setGSID($gsid);
}
