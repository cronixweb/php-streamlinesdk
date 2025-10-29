<?php

namespace Cronixweb\Streamline\Traits;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Utils\StreamlineClient;
use Illuminate\Http\Client\ConnectionException;

class ModelClient
{
    protected string $findOneMethod = "";
    protected string $findAllMethod = "";
    protected string $primaryKey = "";
    protected string $modelName = StreamlineModel::class;
    protected string $dataKey = "";


    public function __construct(private StreamlineClient $client)
    {

    }

    public function find(int $id): StreamlineModel
    {

        $data = $this->client->request($this->findOneMethod, [
            $this->primaryKey => $id,
        ]);

        return ($this->modelName)::parse($data);

    }

    public function client(): StreamlineClient
    {
        return $this->client;
    }

    /**
     * @return StreamlineModel[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function all($body = []): array
    {
        $data = $this->client->request($this->findAllMethod, $body);
        // Dynamically find the list using the key
        if (!isset($data[$this->dataKey]) || !is_array($data[$this->dataKey])) {
            throw new StreamlineApiException("Expected array key '{$this->dataKey}' not found in 'all' response payload.");
        }
        $properties = [];

        foreach ($data[$this->dataKey] as $propertyData) {
            $properties[] = ($this->modelName)::parse($propertyData);
        }

        return $properties;
    }

}