<?php

namespace Cronixweb\Streamline\Traits;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\Property;
use Cronixweb\Streamline\Utils\StreamlineClient;
use Illuminate\Http\Client\ConnectionException;

class ModelClient
{
    protected string $findOneMethod = "";
    protected string $findAllMethod = "";
    protected string $primaryKey = "";
    protected string $modelName = StreamlineModel::class;


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

    /**
     * @return StreamlineModel[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function all(): array
    {
        $data = $this->client->request($this->findAllMethod, [
            $this->primaryKey => 0,
        ]);

        $properties = [];

        foreach ($data as $propertyData) {
            $properties[] = ($this->modelName)::parse($propertyData);
        }

        return $properties;
    }

}