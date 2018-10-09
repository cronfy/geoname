<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:06
 */

namespace cronfy\geoname\common\misc;

use cronfy\geoname\common\models\sqlite\PostalCode;

class PostalCodesSqliteRepository
{
    public $db;

    public function getFindQuery() {
        $query = PostalCode::find();
        $query->db = $this->db;
        return $query;
    }

    /**
     * @param $code string
     * @return PostalCode
     */
    public function getByPostalCode($code, $countryIso) {
        $postalCode = $this->getFindQuery()->andWhere([
            'postal_code' => $code,
            'country_code' => $countryIso,
        ])->one();

        return $postalCode;
    }

}