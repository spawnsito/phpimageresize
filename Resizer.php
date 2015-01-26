<?php


class Resizer {

    public function __construct($path, $configuration=null) {
        if (!($path instanceof ImagePath)) throw new InvalidArgumentException();
        if ($configuration == null)
            $configuration = new Configuration();
    }
}