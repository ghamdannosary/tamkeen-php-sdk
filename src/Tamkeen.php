<?php

namespace PCsoft\Tamkeen;

use GuzzleHttp\Client as HttpClient;
use PCsoft\Tamkeen\Resources\User;

class Tamkeen
{
    use MakesHttpRequests, Actions\ManagesPayments;

    /**
     * The Tamkeen enkKey.
     *
     * @var string
     */
    protected $key;

    /**
     * The Tamkeen Customer CVV Key.
     *
     * @var string
     */
    protected $cvvKey;

    /**
     * The Tamkeen Username.
     *
     * @var string
     */
    protected $username;

    /**
     * The Tamkeen Password.
     *
     * @var string
     */
    protected $password;

    /**
     * The Tamkeen spId.
     *
     * @var string
     */
    protected $serviceId;

    /**
     * The Tamkeen certificate path.
     *
     * @var string
     */
    protected $certificatePath;

    /**
     * The Tamkeen certificate password.
     *
     * @var string
     */
    protected $certificatePassword;

    /**
     * The Guzzle HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new Tamkeen instance.
     *
     * @param  string|null  $apiKey
     * @param  \GuzzleHttp\Client|null  $guzzle
     * @return void
     */
    public function __construct($key = null, $cvvKey = null, $username = null, $password = null, $serviceId = null, string $certificatePath = null, $certificatePassword = null, HttpClient $guzzle = null)
    {
        if (!is_null($key)) {
            $this->setKey($key);
        }

        if (!is_null($cvvKey)) {
            $this->setCvvKey($cvvKey);
        }

        if (!is_null($username)) {
            $this->setUsername($username);
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        if (!is_null($serviceId)) {
            $this->setServiceId($serviceId);
        }

        if (!is_null($certificatePath) && !is_null($certificatePassword)) {
            $this->setCertificate($certificatePath, $certificatePassword);
        }

        if (!is_null($guzzle)) {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * Transform the items of the collection to the given class.
     *
     * @param  array  $collection
     * @param  string  $class
     * @param  array  $extraData
     * @return array
     */
    protected function transformCollection($collection, $class, $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this);
        }, $collection);
    }

    /**
     * Set the merchant key.
     *
     * @param  string $key
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Set the customer cvv key.
     *
     * @param  string $cvvKey
     * @return $this
     */
    public function setCvvKey(int $cvvKey)
    {
        $this->cvvKey = $cvvKey;

        return $this;
    }

    /**
     * Set the merchant username.
     *
     * @param  string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the merchant merchantpassword.
     *
     * @param  string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the merchant serviceId.
     *
     * @param  string $serviceId
     * @return $this
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Set the certificate path.
     *
     * @param  string $serviceId
     * @return $this
     */
    public function setCertificate(string $certificatePath, $certificatePassword)
    {
        $this->certificatePath = $certificatePath;
        $this->certificatePassword = $certificatePassword;

        return $this;
    }

    /**
     * Set a new timeout.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Build Tamkeen.
     *
     * @param  int  $port
     * @return $this
     */
    public function build(int $port)
    {
        $this->guzzle = $this->guzzle ?: new HttpClient([
            'base_uri' => "https://www.tamkeen.com.ye:{$port}/CashPG/api/",
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'encPassword' => $this->encryptPassword($this->key, $this->password),
            ],
        ]);

        return $this;
    }

    /**
     * Encrypt password
     *
     * @return string
     */
    private function encryptPassword($plaintext, $password)
    {
        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);

        return $iv . $hash . $ciphertext;
    }
}
