<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:06
 */

namespace cronfy\geoname\common\misc;

use cronfy\geoname\common\models\sqlite\Geoname;
use cronfy\geoname\common\models\sqlite\Hierarchy;

class HierarchySqliteRepository
{
    public $db;

    public function getFindQuery() {
        $query = Hierarchy::find();
        $query->db = $this->db;
        return $query;
    }

    protected $_pathByChildId = [];
    /**
     * @param $geoname Geoname
     * @return array
     */
    public function getAdmPathToGeoname($geoname) {
        if (!array_key_exists($geoname->geonameid, $this->_pathByChildId)) {
            $result = [];

            // В hierarchy - множественные parent у child'ов.
            //
            // нас интересует только иерархия по ADM, потому что так показывает
            // http://api.geonames.org/hierarchy?geonameId=6295630&username=demo
            // а другие - непонятно, что такое, так что фильтруем по ADM

            $current = $this->getFindQuery()->andWhere(
                [
                    'childId' => $geoname->geonameid,
                    'type' => 'ADM',
                ]
            )->one();

            while ($current) {
                $result[] = $current;
                $current = $this->getFindQuery()->andWhere(
                    [
                        'childId' => $current->parentId,
                        'type' => 'ADM',
                    ]
                )->one();
            }

            $path = array_reverse($result);
            $this->_pathByChildId[$geoname->geonameid] = $path;
        }

        return $this->_pathByChildId[$geoname->geonameid];
    }

}