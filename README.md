PHP dotenv
==========

Loads environment variables from `.env` to `getenv()`, `$_ENV` and
`$_SERVER` automagically.

This is a fork of [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv). Visit that repo for more information.

The Dotenv class now takes an instance of a loader into its constructor. This better allows for different Loader implementations.

InclusionLoader
---------------
The InclusionLoader allows only the values listed in the `include.env` file to be added to $_ENV/$_SERVER. The use-case came about while developing locally. Loading the same .env file across multiple projects lead to unexpected results.