<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 18:18
 */

namespace cronfy\geoname\common\models\sqlite;


use yii\db\ActiveQuery;

class GeonameQuery extends SelectableDbActiveQuery
{
    public function country($countryCode) {
        return $this->andWhere(['country_code' => $countryCode]);
    }

    public function population($min) {
        return $this->andWhere(['>', 'population', $min]);
    }

    public function populatedLocations() {
        $featureClasses = ['P'];

        $notFeatureCodes = [
            # историческое, больше не существует
            'PPLCH',
            'PPLH',
            # Заброшенное
            'PPLQ',
            # Разрушенное
            'PPLW',
            # Часть населенного пункта, например Зюзино. Исключаем, так как
            # для него есть более крупный PPL, который мы импортируем.
            'PPLX',
        ];

        return $this
            ->andWhere(['feature_class' => $featureClasses])
            ->andWhere(['not', ['feature_code' => $notFeatureCodes]])
        ;
    }

}