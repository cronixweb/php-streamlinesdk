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
    protected string $responseKey = "";
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

        $parsedData = [];

        // Check if the response is an array of objects or an object
        if(count(array_filter(array_keys($data[$this->responseKey]), 'is_string')) > 0){
            $data[$this->responseKey] = [$data[$this->responseKey]];
        }

        if($this->responseKey === 'blocked_days'){
            $data[$this->responseKey] = $data[$this->responseKey][0]['blocked'];
        }

        foreach ($data[$this->responseKey] ?? [] as $modelData) {
            $parsedData[] = ($this->modelName)::parse($modelData);
        }

        return $parsedData;
    }

}