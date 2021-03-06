<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:18
 */

namespace cronfy\geoname\common\models\sqlite;


use Yii;
use yii\db\ActiveRecord;

/**
 * @property string $alternate_name
 */
class AlternateName extends ActiveRecord
{
    public static $db;

    public static function getDb()
    {
        return static::$db;
    }

    /**
     * @return AlternateNameQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(AlternateNameQuery::class, [get_called_class()]);
    }
}