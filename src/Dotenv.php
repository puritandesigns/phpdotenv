<?php

namespace Dotenv;

use Dotenv\Contract\LoaderInterface;

/**
 * This is the dotenv class.
 *
 * It's responsible for loading a `.env` file in the given directory and
 * setting the environment vars.
 */
class Dotenv
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The loader instance.
     *
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Create a new dotenv instance.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader) {
        $this->loader = $loader;
    }

    /**
     * Load environment file in given directory.
     *
     * @return array
     */
    public function load()
    {
        return $this->loadData();
    }

    /**
     * Load environment file in given directory.
     *
     * @return array
     */
    public function overload()
    {
        return $this->loadData(true);
    }

    /**
     * Actually load the data.
     *
     * @param bool $overload
     *
     * @return array
     */
    protected function loadData($overload = false)
    {
        return $this->loader->setImmutable(!$overload)->load();
    }

    /**
     * Required ensures that the specified variables exist, and returns a new validator object.
     *
     * @param string|string[] $variable
     *
     * @return \Dotenv\Validator
     */
    public function required($variable)
    {
        return new Validator((array) $variable, $this->loader);
    }

    public function checkIncludesAgainstEnv()
    {
        if (method_exists($this->loader, 'checkIncludesAgainstEnv')) {
            return $this->loader->checkIncludesAgainstEnv();
        }

        return false;
    }
}
