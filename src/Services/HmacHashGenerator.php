<?php

namespace Matthv\AtosSipsGateway\Services;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

class HmacHashGenerator
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var string
     */
    protected string $algorithm = 'HMAC-SHA-256';

    /**
     * HmacHashGenerator constructor.
     *
     * @param Application $app
     *
     * @throws BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->config = $app->make('config');
    }

    /**
     * Get HMAC hash for given params.
     *
     * @param string $data
     *
     * @return string
     */
    public function get(string $data)
    {
        return hash_hmac('sha256', $data, utf8_encode($this->getKey()));
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     *
     * @return HmacHashGenerator
     */
    public function setAlgorithm(string $algorithm): HmacHashGenerator
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get key from file.
     *
     * @return string
     */
    protected function getKey()
    {
        return $this->config->get('atos.secret_key');
    }
}
