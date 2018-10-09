<?php

class m000000_000500_create_postal_code_table extends \yii\db\Migration {

    public function up()
    {
        $this->createTable(
            'postal_code',
            [
                /*
                country code      : iso country code, 2 characters
                postal code       : varchar(20)
                place name        : varchar(180)
                admin name1       : 1. order subdivision (state) varchar(100)
                admin code1       : 1. order subdivision (state) varchar(20)
                admin name2       : 2. order subdivision (county/province) varchar(100)
                admin code2       : 2. order subdivision (county/province) varchar(20)
                admin name3       : 3. order subdivision (community) varchar(100)
                admin code3       : 3. order subdivision (community) varchar(20)
                latitude          : estimated latitude (wgs84)
                longitude         : estimated longitude (wgs84)
                accuracy          : accuracy of lat/lng from 1=estimated to 6=centroid
                */

                'country_code' => $this->char(2),
                'postal_code' => $this->string(20),
                'place_name' => $this->string(100),
                'admin_name1' => $this->string(100),
                'admin_code1' => $this->string(20),
                'admin_name2' => $this->string(100),
                'admin_code2' => $this->string(20),
                'admin_name3' => $this->string(100),
                'admin_code3' => $this->string(20),
                'latitude' => $this->float(5),
                'longitude' => $this->float(5),
                'accuracy' => $this->tinyInteger(),
            ]
        );

        $this->createIndex('postal_code|postal_code', 'postal_code', ['postal_code']);
    }

    public function down()
    {
        $this->dropTable('admin1_code');
    }
}