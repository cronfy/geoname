<?php

namespace cronfy\geoname\common\models;

use cronfy\experience\yii2\ensureSave\EnsureSaveTrait;
use paulzi\jsonBehavior\JsonBehavior;
use paulzi\jsonBehavior\JsonField;

/**
 * @deprecated do not use it at all. It's project specific how to store
 * geonames data. Use this module for import only.
 *
 * This is the model class for table "geoname".
 *
 * @property integer $id
 * @property double $lat
 * @property double $lng
 * @property integer $geonameId
 * @property string $name
 * @property JsonField $data
 * @property string $type
 */
class Geoname extends \yii\db\ActiveRecord
{

    use EnsureSaveTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'lat/number' => ['lat', 'number'],
            'lng/number' => ['lng', 'number'],
            'geonameId/required' => ['geonameId', 'required'],
            'name/required' => ['name', 'required'],
            'geonameId/integer' => ['geonameId', 'integer'],
            'name/length' => ['name', 'string', 'max' => 255],
            'type/length' => ['type', 'string', 'max' => 255],
            'data/string' => ['data', 'string', 'max' => 1024],
            'geonameId/unique' => [['geonameId'], 'unique'],
        ];
    }
    public function behaviors()
    {
        $behaviors = [
            [
                'class' => JsonBehavior::class,
                'attributes' => ['data'],
            ],
        ];

        return $behaviors;
    }

    /**
     * for json behavior: https://github.com/paulzi/yii2-json-behavior/blob/master/README.md#usage-isattributechanged-and-getdirtyattributes
     * @inheritdoc
     */
    public function isAttributeChanged($name, $identical = true)
    {
        if ($this->$name instanceof JsonField) {
            $currentValue = (string) $this->$name;
            $oldValue = (string) $this->getOldAttribute($name);
            return $currentValue !== $oldValue;
        } else {
            return parent::isAttributeChanged($name, $identical);
        }
    }

    /**
     * for json behavior: https://github.com/paulzi/yii2-json-behavior/blob/master/README.md#usage-isattributechanged-and-getdirtyattributes
     * @inheritdoc
     */
    public function getDirtyAttributes($names = null)
    {
        $result = [];
        $data = parent::getDirtyAttributes($names);
        foreach ($data as $name => $value) {
            if ($value instanceof JsonField) {
                $currentValue = (string) $this->$name;
                $oldValue = (string) $this->getOldAttribute($name);
                if ($currentValue !== $oldValue) {
                    $result[$name] = $value;
                }
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'geonameId' => 'Geoname ID',
            'name' => 'Name',
            'data' => 'Data',
        ];
    }

    public function getGeonameDTO() {
        if (!@$this->data['geonameDTO']) {
            return null;
        }

        $geonameDTO = new GeonameDTO();
        foreach ($this->data['geonameDTO'] as $key => $value) {
            $geonameDTO->$key = $value;
        }
        return $geonameDTO;
    }
}
