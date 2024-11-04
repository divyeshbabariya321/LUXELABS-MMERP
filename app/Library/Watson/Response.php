<?php

namespace App\Library\Watson;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use App\Library\Watson\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * The response headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * The response content
     *
     * @var string
     */
    private $content;

    /**
     * The response status code
     *
     * @var int
     */
    private $statusCode;

    /**
     * @var bool
     */
    private $isError = false;

    /**
     * {@inheritdoc}
     */
    public function parse(GuzzleResponse $response)
    {
        $this->setHeaders($response->getHeaders());
        $this->setStatusCode($response->getStatusCode());
        if ($this->getStatusCode() != 200) {
            $this->setIsError(true);
        }
        $this->setContent($response->getBody()->getContents());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess()
    {
        return ($this->getStatusCode() === 200 && $this->isError() == false) ? true : false;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function setIsError(bool $isError)
    {
        $this->isError = $isError;
    }
}
