<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:31
 */

namespace cronfy\geoname\common\misc;


class HierarchyCsvRepository extends GenericCsvRepository
{
    protected $columns = [
        'parentId' => 'parentId',
        'childId' => 'childId',
        'type' => 'The type \'ADM\' stands for the admin hierarchy modeled by the admin1-4 codes. The other entries are entered with the user interface. The relation toponym-adm hierarchy is not included in the file, it can instead be built from the admincodes of the toponym.',
    ];
}