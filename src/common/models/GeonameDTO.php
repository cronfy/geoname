<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 20.08.18
 * Time: 21:24
 */

namespace cronfy\geoname\common\models;


class GeonameDTO
{
    public $geonameid         ; // integer id of record in geonames database
    public $name              ; // name of geographical point (utf8) varchar(200)
    public $asciiname         ; // name of geographical point in plain ascii characters, varchar(200)
    public $alternatenames    ; // alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(10000)
    public $latitude          ; // latitude in decimal degrees (wgs84)
    public $longitude         ; // longitude in decimal degrees (wgs84)
    public $feature_class     ; // see http://www.geonames.org/export/codes.html, char(1)
    public $feature_code      ; // see http://www.geonames.org/export/codes.html, varchar(10)
    public $country_code      ; // ISO-3166 2-letter country code, 2 characters
    public $cc2               ; // alternate country codes, comma separated, ISO-3166 2-letter country code, 200 characters
    public $admin1_code       ; // fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)
    public $admin2_code       ; // code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80)
    public $admin3_code       ; // code for third level administrative division, varchar(20)
    public $admin4_code       ; // code for fourth level administrative division, varchar(20)
    public $population        ; // bigint (8 byte int)
    public $elevation         ; // in meters, integer
    public $dem               ; // digital elevation model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m) or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
    public $timezone          ; // the iana timezone id (see file timeZone.txt) varchar(40)
    public $modification_date ; // date of last modification in yyyy-MM-dd format

}