<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 14:30
 */

namespace cronfy\geoname\common\misc;


class LocalFile extends File
{
    public $path;

    public function open() {
        return fopen($this->path, 'r');
    }

    public function getPath()
    {
        return $this->path;
    }

}