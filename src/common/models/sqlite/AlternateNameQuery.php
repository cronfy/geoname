<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 18:18
 */

namespace cronfy\geoname\common\models\sqlite;


class AlternateNameQuery extends SelectableDbActiveQuery
{
    public function country($countryCode) {
        return $this->andWhere(['countryCode' => $countryCode]);
    }

    public function language($isolanguage) {
        return $this->andWhere(['isolanguage' => $isolanguage]);
    }

}