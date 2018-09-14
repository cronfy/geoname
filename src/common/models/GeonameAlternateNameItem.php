<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 20.08.18
 * Time: 21:24
 */

namespace cronfy\geoname\common\models;


class GeonameAlternateNameItem
{

    public $alternateNameId   ; // the id of this alternate name, int
    public $geonameid         ; // geonameId referring to id in table 'geoname', int
    public $isolanguage       ; // iso 639 language code 2- or 3-characters; 4-characters 'post' for postal codes and 'iata','icao' and faac for airport codes, fr_1793 for French Revolution names,  abbr for abbreviation, link to a website (mostly to wikipedia), wkdt for the wikidataid, varchar(7)
    public $alternate_name    ; // alternate name or name variant, varchar(400)
    public $isPreferredName   ; // '1', if this alternate name is an official/preferred name
    public $isShortName       ; // '1', if this is a short name like 'California' for 'State of California'
    public $isColloquial      ; // '1', if this alternate name is a colloquial or slang term. Example: 'Big Apple' for 'New York'.
    public $isHistoric        ; // '1', if this alternate name is historic and was used in the past. Example 'Bombay' for 'Mumbai'.
    public $from		      ; // from period when the name was used
    public $to		          ; // to period when the name was used

}