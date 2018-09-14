<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 24.08.18
 * Time: 19:33
 */

namespace cronfy\geoname\common\misc;


use cronfy\geoname\common\models\Geoname;

class GeonameRepository
{
    protected $_byGeonameId = [];
    public function getByGeonameId($geonameId) {
        if (!array_key_exists($geonameId, $this->_byGeonameId)) {
            $this->_byGeonameId[$geonameId] = Geoname::find()->where(['geonameId' => $geonameId])->one();
        }

        return $this->_byGeonameId[$geonameId];
    }
}