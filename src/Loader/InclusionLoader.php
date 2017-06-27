<?php

namespace Dotenv\Loader;

class InclusionLoader extends Loader
{
    protected $inclusionFilePath;
    protected $inclusionArray = array();
    protected $ignoreInclusions = false;

    public function __construct(
        $filePath,
        $inclusionFilePath = null,
        $immutable = false
    ) {
        parent::__construct($filePath, $immutable);

        $this->inclusionFilePath = $inclusionFilePath;
        if (! is_null($inclusionFilePath)) {
            $this->loadExclusionFileToArray();
        }
    }

    protected function loadExclusionFileToArray()
    {
        if (
            is_readable($this->inclusionFilePath) ||
            is_file($this->inclusionFilePath)
        ) {
            $filePath = $this->inclusionFilePath;
            $lines = $this->readLinesFromFile($filePath);
            foreach ($lines as $line) {
                if (!$this->isComment($line)) {
                    if ($this->looksLikeSetter($line)) {
                        $this->ignoreInclusions(true);
                        $this->setEnvironmentVariable($line);
                        $this->ignoreInclusions(false);
                    }

                    $this->inclusionArray[] = $line;
                }
            }
        }

        return $this->inclusionArray;
    }

    protected function loadFileIntoArray($filePath, $withValues = false)
    {
        if (!is_readable($filePath) || !is_file($filePath)) {
            return false;
        }

        $data = [];

        $lines = $this->readLinesFromFile($filePath);
        foreach ($lines as $line) {
            if (!$this->isComment($line)) {
                $value = null;
                list($name, $value) = $this->normaliseEnvironmentVariable($line, $value);

                if ($withValues) {
                    $key = $name;
                } else {
                    $value = $name;
                    $key = count($data);
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function ignoreInclusions($bool = true)
    {
        $this->ignoreInclusions = $bool;
    }

    public function checkIncludesAgainstEnv()
    {
        $value = null;
        $no_matches = array();
        foreach ($this->inclusionArray as $name) {
            list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);
            if (! isset($_ENV[$name])) {
                $no_matches[] = $name;
            }
        }

        return $no_matches;
    }

    public function compareEnvFiles($filePath1, $filePath2)
    {
        $array1 = $this->loadFileIntoArray($filePath1);
        $array2 = $this->loadFileIntoArray($filePath2);

        if (! $array1 || ! $array2) {
            return false;
        }

        return array_diff($array1, $array2);
    }

    public function setEnvironmentVariable($name, $value = null)
    {
        list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);

        // Don't overwrite existing environment variables if we're immutable
        // Ruby's dotenv does this with `ENV[key] ||= value`.
        if ($this->immutable && $this->getEnvironmentVariable($name) !== null) {
            return;
        }

        // If we are NOT ignoring the inclusion array
        // and $name is not in inclusion, then return.
        if (
            ! $this->ignoreInclusions &&
            ! empty($this->inclusionArray) &&
            ! in_array($name, $this->inclusionArray)
        ) {
            return;
        }

        // If PHP is running as an Apache module and an existing
        // Apache environment variable exists, overwrite it
        if (
            function_exists('apache_getenv') &&
            function_exists('apache_setenv') && apache_getenv($name)
        ) {
            apache_setenv($name, $value);
        }

        if (function_exists('putenv')) {
            putenv("$name=$value");
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}