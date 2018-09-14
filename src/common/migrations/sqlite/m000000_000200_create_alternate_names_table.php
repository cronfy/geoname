<?php

class m000000_000200_create_alternate_names_table extends \yii\db\Migration {

    public function up()
    {
        $this->createTable(
            'alternate_name',
            [
                // NOT from geonames, to control init by country
                'countryCode' => $this->char(2),


                // the id of this alternate name, int
                'alternateNameId' => $this->primaryKey(),
                // geonameId referring to id in table 'geoname', int
                'geonameid' => $this->integer()->notNull(),
                // iso 639 language code 2- or 3-characters; 4-characters 'post' for postal codes and 'iata','icao' and faac for airport codes, fr_1793 for French Revolution names,  abbr for abbreviation, link to a website (mostly to wikipedia), wkdt for the wikidataid, varchar(7)
                'isolanguage' => $this->string(7),
                // alternate name or name variant, varchar(400)
                'alternate_name' => $this->string(400),
                // '1', if this alternate name is an official/preferred name
                'isPreferredName' => $this->boolean(),
                // '1', if this is a short name like 'California' for 'State of California'
                'isShortName' => $this->boolean(),
                // '1', if this alternate name is a colloquial or slang term. Example: 'Big Apple' for 'New York'.
                'isColloquial' => $this->boolean(),
                // '1', if this alternate name is historic and was used in the past. Example 'Bombay' for 'Mumbai'.
                'isHistoric' => $this->boolean(),
                // from period when the name was used
                'from' => $this->date(),
                // to period when the name was used
                'to' => $this->date(),
            ]
        );

        $this->createIndex('geonameid', 'alternate_name', ['geonameid']);
    }
}