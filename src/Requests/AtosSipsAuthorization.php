<?php

namespace Matthv\AtosSipsGateway\Requests;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Matthv\AtosSipsGateway\Currency;
use Matthv\AtosSipsGateway\Services\HmacHashGenerator;

class AtosSipsAuthorization
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var string|null
     */
    protected $paymentNumber = null;

    /**
     * @var int
     */
    protected int $amount = 0;

    /**
     * @var string|null
     */
    protected ?string $currencyCode = null;

    /**
     * @var array
     */
    protected array $customParameters = [];

    /**
     * @var ViewFactory
     */
    private ViewFactory $view;

    /**
     * @var HmacHashGenerator
     */
    protected HmacHashGenerator $seal;

    /**
     * @var UrlGenerator
     */
    protected UrlGenerator $urlGenerator;

    /**
     * @var string
     */
    protected ?string $returnUrl = '';

    /**
     * @var string
     */
    protected ?string $callbackUrl = '';

    /**
     * AtosSipsAuthorization constructor.
     *
     * @param Config            $config
     * @param ViewFactory       $view
     * @param HmacHashGenerator $seal
     * @param UrlGenerator      $urlGenerator
     */
    public function __construct(
        Config $config,
        ViewFactory $view,
        HmacHashGenerator $seal,
        UrlGenerator $urlGenerator
    ) {
        $this->config = $config;
        $this->view = $view;
        $this->seal = $seal;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public function getBasicParameters(): array
    {
        return [
            'amount'                => $this->amount,
            'currencyCode'          => $this->currencyCode,
            'transactionReference'  => $this->paymentNumber,
            'merchantId'            => $this->config->get('atos.merchant_id'),
            'keyVersion'            => $this->config->get('atos.key_version'),
            'normalReturnUrl'       => $this->getCustomerUrl('returnUrl', 'customer_return_route_name'),
            'automaticResponseUrl'  => $this->getCustomerUrl('callbackUrl', 'customer_callback_route_name'),
        ];
    }

    /**
     * @return array
     */
    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }

    /**
     * @param array $customParameters
     *
     * @return AtosSipsAuthorization
     */
    public function setCustomParameters(array $customParameters): AtosSipsAuthorization
    {
        $this->customParameters = $customParameters;
        return $this;
    }

    /**
     * @return string
     */
    protected function getData(): string
    {
        $params = '';
        $parameters = array_merge($this->getBasicParameters(), $this->getCustomParameters());

        foreach($parameters as $key => $value) {
            $params .= $key . '=' . $value . '|';
        }

        return utf8_encode(rtrim($params, '|'));
    }

    /**
     * Get customer url.
     *
     * @param string $variableName
     * @param string $configKey
     *
     * @return string
     */
    protected function getCustomerUrl($variableName, $configKey)
    {
        return $this->$variableName ?: $this->urlGenerator->route(
            $this->config->get('atos.' . $configKey)
        );
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        $prefix = $this->config->get('atos.test') ? 'test' : 'production';

        return $this->config->get('atos.' . $prefix . '_url');
    }

    /**
     * @param int    $amount
     * @param string $currencyCode
     *
     * @return AtosSipsAuthorization
     */
    public function setAmount(int $amount, $currencyCode = Currency::EUR): AtosSipsAuthorization
    {
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function setPaymentNumber($number): AtosSipsAuthorization
    {
        $this->paymentNumber = $number;
        return $this;
    }

    /**
     * Start payment flow.
     *
     * @param string|null $viewName a custom view that will receive the payment information in its controller
     *
     * @return View
     */
    public function paymentView(?string $viewName = null): View
    {
        $data = $this->getData();
        if (null === $viewName) {
            $viewName = 'vendor.atos.send';
        }

        return $this->view->make(
            $viewName,
            [
                'url'       => $this->getUrl(),
                'fields'    => [
                    'Data'              => $data,
                    'InterfaceVersion'  => $this->config->get('atos.interface_version'),
                    'Seal'              => $this->seal->get($data),
                    'SealAlgorithm'     => $this->seal->getAlgorithm(),
                ],
            ]
        );
    }
}
