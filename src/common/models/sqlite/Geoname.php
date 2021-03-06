<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:18
 */

namespace cronfy\geoname\common\models\sqlite;


use cronfy\geoname\common\models\GeonameDTO;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property integer $geonameid
 * @property float $latitude
 * @property float $longitude
 * @property string $name
 */
class Geoname extends ActiveRecord
{
    public static $db;

    public static function getDb()
    {
        return static::$db;
    }

    /**
     * @return GeonameQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(GeonameQuery::class, [get_called_class()]);
    }

    public function getDto() {
        $dto = new GeonameDTO();
        foreach (get_object_vars($dto) as $key => $value) {
            $dto->$key = $this->$key;
        }
        return $dto;
    }
}