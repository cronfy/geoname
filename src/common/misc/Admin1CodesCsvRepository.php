<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:31
 */

namespace cronfy\geoname\common\misc;


class Admin1CodesCsvRepository extends GenericCsvRepository
{
    protected $columns = [
        'code' => 'code',
        'name' => 'name',
        'name ascii' => 'name ascii',
        'geonameid' => 'geonameid',
    ];
}