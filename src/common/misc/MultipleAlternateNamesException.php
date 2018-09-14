<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 21.08.18
 * Time: 18:48
 */

namespace cronfy\geoname\common\misc;


class MultipleAlternateNamesException extends \Exception
{
    public $names;
}