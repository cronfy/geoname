<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:06
 */

namespace cronfy\geoname\common\misc;


use cronfy\geoname\common\models\sqlite\AlternateName;

class AlternateNamesSqliteRepository
{
    public $db;

    public function getFindQuery() {
        $query = AlternateName::find();
        $query->db = $this->db;
        return $query;
    }

    public function getIdsOfKnownErrors() {
        $errorAlternameNameIds = [
            5964289, // "Совецк" - некоррекное название для ru
            1727316, // "Славянск На Кубани" - некоррекное название для ru
            1742902, // "Щекино" - правильно Щёкино - ru
            1684836, // "Очер" - правильно Очёр - ru

            1711311, // "Буденновск" - е вместо ё - ru
            1758535, // "Киселевск" - е вместо ё - ru
            1663464, // "Березовский" - е вместо ё - ru
            1695817, // "Березовка" - е вместо ё - ru
            1731130, // "Артемовский" - е вместо ё - ru
            1681102, // "Артем" - е вместо ё - ru
            1689052, // "Линево" - е вместо ё - ru
            1739505, // "Белая-Калитва" - дефис не нужен - ru
            11402227, // "Адлерский район" vs Адлер - лучше Адлер - ru
            1699213, // "Новомаклаково" историческое название - ru
            1765055, // "Ивдель-1" неверное название - ru


            2728021, // ru Пе́рмский край
            2298561, // Пермская область - историческое название
        ];

        return $errorAlternameNameIds;
    }
}