<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 15:03
 */

namespace cronfy\geoname\common\misc;


abstract class LocalFileProxy extends File
{
    /**
     * @return LocalFile
     */
    abstract protected function getLocalFile();

    public function open()
    {
        return $this->getLocalFile()->open();
    }

    public function getPath()
    {
        return $this->getLocalFile()->getPath();
    }

}