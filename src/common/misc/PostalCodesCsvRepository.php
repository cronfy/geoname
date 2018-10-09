<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:31
 */

namespace cronfy\geoname\common\misc;


class PostalCodesCsvRepository extends GenericCsvRepository
{
    protected $columns = [
        'country_code' => '',
        'postal_code' => '',
        'place_name' => '',
        'admin_name1' => '',
        'admin_code1' => '',
        'admin_name2' => '',
        'admin_code2' => '',
        'admin_name3' => '',
        'admin_code3' => '',
        'latitude' => '',
        'longitude' => '',
        'accuracy' => '',
    ];
}