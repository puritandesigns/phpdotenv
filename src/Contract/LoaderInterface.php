<?php

namespace Dotenv\Contract;

/**
 * @author mkelly
 * @date 6/27/17
 */
interface LoaderInterface
{
    /**
     * Set immutable value.
     *
     * @param bool $immutable
     * @return $this
     */
    public function setImmutable($immutable = false);

    /**
     * Set immutable value.
     *
     * @return bool
     */
    public function getImmutable();

    /**
     * Load `.env` file in given directory.
     *
     * @return array
     */
    public function load();

    /**
     * Process the runtime filters.
     *
     * Called from the `VariableFactory`, passed as a callback in `$this->loadFromFile()`.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     */
    public function processFilters($name, $value);

    /**
     * Search the different places for environment variables and return first value found.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getEnvironmentVariable($name);

    /**
     * Set an environment variable.
     *
     * This is done using:
     * - putenv,
     * - $_ENV,
     * - $_SERVER.
     *
     * The environment variable value is stripped of single and double quotes.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function setEnvironmentVariable($name, $value = null);

    /**
     * Clear an environment variable.
     *
     * This is not (currently) used by Dotenv but is provided as a utility
     * method for 3rd party code.
     *
     * This is done using:
     * - putenv,
     * - unset($_ENV, $_SERVER).
     *
     * @param string $name
     *
     * @see setEnvironmentVariable()
     *
     * @return void
     */
    public function clearEnvironmentVariable($name);
}