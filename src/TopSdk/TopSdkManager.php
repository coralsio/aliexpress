<?php

namespace Corals\Modules\Aliexpress\TopSdk;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TopSdkManager
{
    /**
     * @var \TopClient
     */
    protected $client;

    protected $page_size = 50;

    public function __construct($appKey, $secretKey)
    {
        require_once 'TopSdk.php';

        $topClient = new \TopClient();

        $topClient->appkey = $appKey;
        $topClient->secretKey = $secretKey;

        $this->client = $topClient;
    }

    protected function responseHandler($response)
    {
        $respCode = Arr::get($response, 'resp_result.resp_code');

        $code = Arr::get($response, 'code');

        if ($code) {
            throw new \Exception(json_encode($response));
        }

        switch ($respCode) {
            case 200:
            case 405:
                return Arr::get($response, 'resp_result.result', []);
            default:
                throw new \Exception(Arr::get($response, 'resp_result.resp_msg'));
        }
    }

    /**
     * @return array|\ArrayAccess|mixed
     * @throws \Exception
     */
    public function categoryGetRequest()
    {
        $request = new \AliexpressAffiliateCategoryGetRequest();

        $request->setAppSignature(Str::random());

        return $this->responseHandler($this->client->execute($request));
    }

    /**
     * @param string $keywords
     * @param array $parameters
     * @param int $page
     * @return array|\ArrayAccess|mixed
     * @throws \Exception
     */
    public function productQueryRequest(string $keywords, array $parameters = [], $page = 1)
    {
        $request = new \AliexpressAffiliateProductQueryRequest();

        $request->setAppSignature(Str::random());

        $request->setKeywords($keywords);

        $request->setPageNo($page);

        $request->setPageSize($this->page_size);

        foreach ($parameters as $key => $value) {
            $setMethod = sprintf('set%s', ucfirst($key));

            if (empty($value) || ! method_exists($request, $setMethod)) {
                continue;
            }

            $request->{$setMethod}($value);
        }

        $response = $this->client->execute($request);

        return $this->responseHandler($response);
    }
}
