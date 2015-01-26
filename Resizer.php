<?php


class Resizer {

    public function __construct($path, $configuration=null) {
        $this->checkPath($path);
        if ($configuration == null)
            $configuration = new Configuration();
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

    private function checkPath($path) {
        if (!($path instanceof ImagePath)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if ($configuration == null)
            $configuration = new Configuration();
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }
}