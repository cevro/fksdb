<?php

namespace FKSDB\Components\Grids;

use FKSDB\Components\Grids\BaseGrid;
use Nette\Database\Table\Selection;
use SQL\SearchableDataSource;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class SchoolsGrid extends BaseGrid {

    protected function configure($presenter) {
        parent::configure($presenter);

        //
        // data
        //
        $serviceSchool = $presenter->context->getService('ServiceSchool');
        $schools = $serviceSchool->getSchools();

        $dataSource = new SearchableDataSource($schools);
        $dataSource->setFilterCallback(function(Selection $table, $value) {
                    $tokens = preg_split('/\s+/', $value);
                    foreach ($tokens as $token) {
                        $table->where('name_full LIKE CONCAT(\'%\', ? , \'%\')', $token);
                    }
                });
        $this->setDataSource($dataSource);

        //
        // columns
        //
        $this->addColumn('name', 'Název');
        $this->addColumn('city', 'Město');

        //
        // operations
        //
        $that = $this;
        $this->addButton("edit", "Upravit")
                ->setText('Upravit') //todo i18n
                ->setLink(function($row) use ($that) {
                            return $that->getPresenter()->link("edit", $row->school_id);
                        });
        $this->addGlobalButton('add')
                ->setLabel('Vložit školu')
                ->setClass('btn btn-sm btn-primary');

        //
        // appeareance
    //

    }

}
