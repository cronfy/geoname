<?php

class m000000_000100_create_geoname_table extends \yii\db\Migration {

    public function up()
    {
        $this->createTable(
            'geoname',
            [
                'id' => $this->primaryKey(),

                // integer id of record in geonames database
                'geonameid' => $this->integer()->notNull(),
                // name of geographical point (utf8) varchar(200)
                'name' => $this->string(200),
                // name of geographical point in plain ascii characters, varchar(200)
                'asciiname' => $this->string(200),
                // alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(10000)
                'alternatenames' => $this->string(10000),
                // latitude in decimal degrees (wgs84)
                'latitude' => $this->float(5),
                // longitude in decimal degrees (wgs84)
                'longitude' => $this->float(5),
                // see http://www.geonames.org/export/codes.html, char(1)
                'feature_class' => $this->char(1),
                // name of geographical point in plain ascii characters, varchar(200)
                'feature_code' => $this->string(10),
                // ISO-3166 2-letter country code, 2 characters
                'country_code' => $this->char(2),
                // alternate country codes, comma separated, ISO-3166 2-letter country code, 200 characters
                'cc2' => $this->string(200),
                // fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)
                'admin1_code' => $this->string(20),
                // code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80)
                'admin2_code' => $this->string(80),
                // code for third level administrative division, varchar(20)
                'admin3_code' => $this->string(20),
                // code for fourth level administrative division, varchar(20)
                'admin4_code' => $this->string(20),
                // bigint (8 byte int)
                'population' => $this->bigInteger(),
                // in meters, integer
                'elevation' => $this->integer(),
                // digital elevation model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m) or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
                'dem' => $this->integer(),
                // the iana timezone id (see file timeZone.txt) varchar(40)
                'timezone' => $this->string(40),
                // date of last modification in yyyy-MM-dd format
                'modification_date' => $this->date(),
            ]
        );

        $this->createIndex('geoname|geonameid', 'geoname', ['geonameid']);
    }
}