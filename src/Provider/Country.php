<?php


namespace League\OAuth2\Client\Provider;


use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class Country
{
    use ArrayAccessorTrait;

    private $country;

    /**
     * Address constructor.
     * @param array $country
     */
    public function __construct(array $country = array())
    {
        $this->country = $country;
    }

    public function getName()
    {
        return $this->getValueByKey($this->country, 'name');
    }

    public function getAlpha2()
    {
        return $this->getValueByKey($this->country, 'alpha2');
    }

    public function getAlpha3()
    {
        return $this->getValueByKey($this->country, 'alpha3');
    }

    public function toArray()
    {
        return $this->country;
    }
}
