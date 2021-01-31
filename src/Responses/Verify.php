<?php

namespace Matthv\AtosSipsGateway\Responses;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Matthv\AtosSipsGateway\Exceptions\InvalidSignature;
use Matthv\AtosSipsGateway\ResponseCode;
use Matthv\AtosSipsGateway\ResponseKey;
use Matthv\AtosSipsGateway\Services\HmacHashGenerator;

class Verify
{
    public const FIELD_RESPONSE_CODE = 'responseCode';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var HmacHashGenerator
     */
    private HmacHashGenerator $seal;

    /**
     * @var Collection
     */
    private Collection $parameters;

    /**
     * Verify constructor.
     *
     * @param Request           $request
     * @param Config            $config
     * @param HmacHashGenerator $seal
     */
    public function __construct(
        Request $request,
        Config $config,
        HmacHashGenerator $seal)
    {
        $this->request = $request;
        $this->config = $config;
        $this->seal = $seal;
        $this->setParameters();
    }

    /**
     * Verify if request is successful.
     * @return bool
     * @throws InvalidSignature
     */
    public function isSuccess(): bool
    {
        return $this->checkSignature(
            $this->request->input(ResponseKey::DATA),
            $this->request->input(ResponseKey::SEAL)
        ) && $this->getParameter(self::FIELD_RESPONSE_CODE) === ResponseCode::SUCCESS;
    }

    /**
     * Verify whether given signature is correct.
     * @param string $data
     * @param string $sealData
     *
     * @return bool
     * @throws InvalidSignature
     */
    public function checkSignature(string $data, string $sealData): bool
    {
        if ($this->seal->get($data) !== $sealData) {
            throw new InvalidSignature();
        }

        return true;
    }

    /**
     * Return the value of a a specific field in the callback data
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getParameter(string $key): ?string
    {
        return $this->parameters->get($key);
    }

    /**
     * @return Collection
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    /**
     * @return Verify
     */
    protected function setParameters(): Verify
    {
        $this->parameters = new Collection();
        foreach(explode('|', $this->request->input(ResponseKey::DATA)) as $input) {
            $parameter = explode('=', $input);
            $this->parameters->put($parameter[0], $parameter[1] === 'null' ? null : $parameter[1]);
        }

        return $this;
    }
}
