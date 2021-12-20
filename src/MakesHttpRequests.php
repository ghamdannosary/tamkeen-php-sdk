<?php

namespace PCsoft\Tamkeen;

use Exception;
use PCsoft\Tamkeen\Exceptions\FailedActionException;
use PCsoft\Tamkeen\Exceptions\NotFoundException;
use PCsoft\Tamkeen\Exceptions\TimeoutException;
use PCsoft\Tamkeen\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

trait MakesHttpRequests
{
    /**
     * Make a GET request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @return mixed
     */
    public function get($uri)
    {
        return $this->request('GET', $uri);
    }

    /**
     * Make a POST request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function post($uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function put($uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a DELETE request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function delete($uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make request to Tamkeen servers and return the response.
     *
     * @param  string  $verb
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    protected function request($verb, $uri, array $payload = [])
    {
        $timestamp = round(microtime(true) * 1000);
        $token = md5($this->serviceId . $this->username . $timestamp);
        $request_id = strtoupper($this->username) . '_' . $timestamp . '_' . rand();

        $response = $this->guzzle->request($verb, $uri, [
            'json' => $payload + [
                'RequestID' => $request_id,
                'UserName' => $this->username,
                'SpId' => $this->serviceId,
                'MDToken' => $token,
            ],
            'headers' => ['unixtimestamp' => $timestamp],
            'cert' => [$this->certificatePath, $this->certificatePassword],
            'curl' => [CURLOPT_SSLCERTTYPE => 'P12'], // Define it's a PFX key
            'verify' => true,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode > 299) {
            return $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    /**
     * Handle the request error.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     *
     * @throws \Exception
     * @throws \PCsoft\Tamkeen\Exceptions\FailedActionException
     * @throws \PCsoft\Tamkeen\Exceptions\NotFoundException
     * @throws \PCsoft\Tamkeen\Exceptions\ValidationException
     */
    protected function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException();
        }

        if ($response->getStatusCode() == 400) {
            throw new FailedActionException((string) $response->getBody());
        }

        throw new Exception((string) $response->getBody());
    }

    /**
     * Retry the callback or fail after x seconds.
     *
     * @param  int  $timeout
     * @param  callable  $callback
     * @param  int  $sleep
     * @return mixed
     *
     * @throws \PCsoft\Tamkeen\Exceptions\TimeoutException
     */
    public function retry($timeout, $callback, $sleep = 5)
    {
        $start = time();

        beginning:

        if ($output = $callback()) {
            return $output;
        }

        if (time() - $start < $timeout) {
            sleep($sleep);

            goto beginning;
        }

        throw new TimeoutException($output);
    }
}
