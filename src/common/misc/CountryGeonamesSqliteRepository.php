<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:06
 */

namespace cronfy\geoname\common\misc;


use cronfy\geoname\common\models\sqlite\Geoname;

class CountryGeonamesSqliteRepository
{
    public $db;

    public function getFindQuery() {
        $query = Geoname::find();
        $query->db = $this->db;
        return $query;
    }

    protected $_byGeonameId = [];
    /**
     * @param $geonameId
     * @return Geoname
     */
    public function getByGeonameId($geonameId) {
        if (!array_key_exists($geonameId, $this->_byGeonameId)) {
            $geoname = $this->getFindQuery()->andWhere(['geonameid' => $geonameId])->one();
            $this->_byGeonameId[$geonameId] = $geoname;
        }

        return $this->_byGeonameId[$geonameId];
    }
}