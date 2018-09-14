<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 18:55
 */

namespace cronfy\geoname\common\models\sqlite;


use yii\db\ActiveQuery;

class SelectableDbActiveQuery extends ActiveQuery
{
    public $db;

    public function all($db = null)
    {
        return parent::all($db ?: $this->db);
    }

    public function one($db = null)
    {
        return parent::one($db ?: $this->db);
    }

    public function exists($db = null)
    {
        return parent::exists($db ?: $this->db);
    }

    public function batch($batchSize = 100, $db = null)
    {
        $db = $db ?: $this->db;
        return parent::batch($batchSize, $db);
    }

}