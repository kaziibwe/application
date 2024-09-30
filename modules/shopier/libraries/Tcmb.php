<?php

/**
 * Facade class for TCMB currency operations
 *
 * Class Tcmb
 */
class Tcmb
{
    /**
     * Create TCMB client
     *
     * @return TcmbClient
     */
    public function createClient()
    {
        return new TcmbClient();
    }

    /**
     * Create currency reader
     *
     * @return TcmbCurrencyReader
     */
    public function createCurrencyReader()
    {
        return new TcmbCurrencyReader($this->createClient());
    }

    /**
     * Create currency converter
     *
     * @return TcmbCurrencyConverter
     */
    public function createCurrencyConverter()
    {
        return new TcmbCurrencyConverter($this->createCurrencyReader());
    }
}

/**
 * XML client abstraction
 *
 * Interface ICurrencyXmlClient
 */
interface ICurrencyXmlClient
{
    /**
     * @return SimpleXMLElement
     */
    public function read();

    /**
     * @return SimpleXMLElement
     */
    public function getXml();
}

/**
 * Currency reader abstraction
 *
 * Interface ICurrencyReader
 */
interface ICurrencyReader
{
    public function __construct(ICurrencyXmlClient $client);

    /**
     * @param $currencyCode
     * @param $fieldName
     * @return float
     */
    public function readCurrency($currencyCode, $fieldName);

    /**
     * @param $currencyCode
     * @return float
     */
    public function readBuying($currencyCode);

    /**
     * @param $currencyCode
     * @return float
     */
    public function readSelling($currencyCode);
}

/**
 * Currency converter abstraction
 *
 * Interface ICurrencyConverter
 */
interface ICurrencyConverter
{
    /**
     * ICurrencyConverter constructor.
     * @param ICurrencyReader $currencyReader
     */
    public function __construct(ICurrencyReader $currencyReader);

    /**
     * @param $amount
     * @param string $fromCurrencyCode
     * @return mixed
     */
    public function convertForBuying($amount, $fromCurrencyCode);

    /**
     * @param $amount
     * @param string $toCurrencyCode
     * @return mixed
     */
    public function convertForSelling($amount, $toCurrencyCode);

    /**
     * @param $amount
     * @param $fromCurrencyCode
     * @return mixed
     */
    public function convertToTRY($amount, $fromCurrencyCode);

    /**
     * @param $amount
     * @param $toCurrencyCode
     * @return mixed
     */
    public function convertFromTRY($amount, $toCurrencyCode);
}

/**
 * Client for access XML resource on TCMB
 *
 * Class TcmbClient
 */
class TcmbClient implements ICurrencyXmlClient
{
    const TCMB_CURRENCIES_XML_URL = 'https://www.tcmb.gov.tr/kurlar/today.xml';

    /**
     * curl resource
     */
    protected $curl;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * TcmbClient constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->initCurl($options);
    }

    /**
     * @param array $options
     */
    protected function initCurl(array $options = [])
    {
        $this->prepareOptions($options);
        $this->curl = curl_init();

        foreach ($this->options as $key => $val) {
            curl_setopt($this->curl, $key, $val);
        }
    }

    /**
     * @param array $options
     * @return array
     */
    protected function prepareOptions(array $options = [])
    {
        $this->options = [
            CURLOPT_REFERER => 'https://www.google.com',
            CURLOPT_URL => self::TCMB_CURRENCIES_XML_URL,
            CURLOPT_RETURNTRANSFER => true,
        ];

        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
        return $this->options;
    }

    /**
     * @return SimpleXMLElement
     */
    public function read()
    {
        $response = curl_exec($this->curl);
        $this->xml = simplexml_load_string($response);
        return $this->xml;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

}

/**
 * Currency reader via client
 *
 * Class TcmbCurrencyReader
 */
class TcmbCurrencyReader implements ICurrencyReader
{
    /**
     * @var ICurrencyXmlClient
     */
    protected $client;

    /**
     * TcmbCurrencyReader constructor.
     * @param ICurrencyXmlClient $client
     */
    public function __construct(ICurrencyXmlClient $client)
    {
        $this->client = $client;
        $this->client->read();
    }

    /**
     * @param $currencyCode
     * @return SimpleXMLElement|null
     */
    protected function findNode($currencyCode)
    {
        $node = $this->client->getXml()->Currency;
        $count = $node->count();
        for ($i = 0; $i < $count; $i++) {
            if ($node[$i]->attributes()->CurrencyCode == $currencyCode)
                return $node[$i];
        }

        return null;
    }

    /**
     * @param $currencyCode
     * @param $fieldName
     * @return SimpleXMLElement|null
     */
    protected function readNodeValue($currencyCode, $fieldName)
    {
        $node = $this->findNode($currencyCode);
        if (!$node) return null;

        return $node->{$fieldName};
    }

    /**
     * @param $currencyCode
     * @param $fieldName
     * @return float
     */
    public function readCurrency($currencyCode, $fieldName)
    {
        return (float)$this->readNodeValue($currencyCode, $fieldName);
    }

    /**
     * @param $currencyCode
     * @return float
     */
    public function readBuying($currencyCode)
    {
        return $this->readCurrency($currencyCode, 'ForexBuying');
    }

    /**
     * @param $currencyCode
     * @return float
     */
    public function readSelling($currencyCode)
    {
        return $this->readCurrency($currencyCode, 'ForexSelling');
    }

}

/**
 * Currency converter via reader
 *
 * Class TcmbCurrencyConverter
 */
class TcmbCurrencyConverter implements ICurrencyConverter
{
    /**
     * @var ICurrencyReader
     */
    protected $currencyReader;

    /**
     * CurrencyConverter constructor.
     * @param ICurrencyReader $currencyReader
     */
    public function __construct(ICurrencyReader $currencyReader)
    {
        $this->currencyReader = $currencyReader;
    }

    /**
     * @inheritDoc
     */
    public function convertForBuying($amount, $fromCurrencyCode)
    {
        return floatval($amount) * $this->currencyReader->readBuying($fromCurrencyCode);
    }

    /**
     * @inheritDoc
     */
    public function convertForSelling($amount, $toCurrencyCode)
    {
        return floatval($amount) / $this->currencyReader->readSelling($toCurrencyCode);
    }

    /**
     * @param $amount
     * @param $fromCurrencyCode
     * @return float|int
     */
    public function convertToTRY($amount, $fromCurrencyCode)
    {
        return floatval($amount) * $this->currencyReader->readSelling($fromCurrencyCode);
    }

    /**
     * @param $amount
     * @param $toCurrencyCode
     * @return float|int
     */
    public function convertFromTRY($amount, $toCurrencyCode)
    {
        return floatval($amount) / $this->currencyReader->readSelling($toCurrencyCode);
    }
}