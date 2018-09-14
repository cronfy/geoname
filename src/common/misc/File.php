<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 14:25
 */

namespace cronfy\geoname\common\misc;


abstract class File
{
    /**
     * @return resource opened file handle
     */
    abstract public function open();

    /**
     * @return string path to file, returned path must point to existing file
     */
    abstract public function getPath();
}