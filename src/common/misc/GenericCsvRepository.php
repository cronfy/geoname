<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 18:59
 */

namespace cronfy\geoname\common\misc;


class GenericCsvRepository
{
    /** @var File */
    public $sourceFile;

    protected $columns = [];

    public function iterate() {
        $fh = $this->sourceFile->open();

        $columnNames = array_keys($this->columns);
        $columnNames = array_map(
            function ($item) {
                return strtr($item, ' ', '_');
            },
            $columnNames
        );

        while ($line = fgetcsv($fh, 0, "\t")) {
            $lineData = array_combine($columnNames, $line);

            yield $lineData;
        }
    }
}