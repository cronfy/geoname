<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:06
 */

namespace cronfy\geoname\common\misc;


use cronfy\geoname\common\models\GeonameDTO;
use cronfy\geoname\common\models\sqlite\Admin1Code;

class Admin1CodesSqliteRepository
{
    public $db;

    public function getFindQuery() {
        $query = Admin1Code::find();
        $query->db = $this->db;
        return $query;
    }

    protected $_byCode = [];
    /**
     * @param $geoname GeonameDTO
     * @return Admin1Code
     */
    public function getByGeoname($geoname) {
        $code = $geoname->country_code . '.' . $geoname->admin1_code;
        if (!array_key_exists($code, $this->_byCode)) {
            $adminCode = $this->getFindQuery()->andWhere(['code' => $code])->one();
            $this->_byCode[$code] = $adminCode;
        }

        return $this->_byCode[$code];
    }

}