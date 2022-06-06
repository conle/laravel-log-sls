<?php

namespace Conle\LaravelLogSLS;

use AliSlsLog\Aliyun_Log_Client;
use AliSlsLog\Models\Aliyun_Log_Models_LogItem;
use AliSlsLog\Models\Request\Aliyun_Log_Models_PutLogsRequest;
use AliSlsLog\SaveLogSls;
use Illuminate\Support\Arr;
use Conle\LaravelLogSLS\Helpers\ArrayHelper;
use Illuminate\Support\Facades\Log;
use Conle\LaravelLogSLS\Exceptions\SLSException;

class SLSLog
{

    /**
     *
     * @var string
     */
    private $accessKeyId;

    /**
     *
     * @var string
     */
    private $accessKeySecret;

    /**
     *
     * @var string
     */
    private $endpoint;

    /**
     *
     * @var string
     */
    private $project;

    /**
     *
     * @var string
     */
    private $logStore;

    /**
     *
     * @var string
     */
    private $topic;

    /**
     *
     * @var string
     */
    private $source;

    /**
     *
     * @var string
     */
    private $errorLogChannel = 'daily';

    /**
     *
     * @var Client
     */
    private $client;

    /**
     *
     * @var array
     */
    private static $config;

    /**
     *
     * @var string
     */
    private $store = 'default';

    public function __construct(array $config)
    {
        static::$config = $config;
        $store = ArrayHelper::getValue(static::$config, 'store');
        $store && $this->setStore($store);
        $this->loadConfig();

        if (is_null($this->endpoint) || is_null($this->accessKeyId) || is_null($this->accessKeySecret) || is_null($this->project) || is_null($this->logStore)) {
            throw new \RuntimeException('Warning: accesskeyid, accesskeysecret, endpoint, project, logStore cannot be empty.');
        }
        $this->client = new Aliyun_Log_Client($this->endpoint, $this->accessKeyId, $this->accessKeySecret);
    }


    public function putLogs(array $contents)
    {
        try {
            if (is_null($this->client)) {
                throw new \RuntimeException('Warning: accesskeyid, accesskeysecret, endpoint, project, logStore cannot be empty.');
            }
            $depth = ArrayHelper::getDepth($contents);
            if ($depth == 1) {
                $logItems = [
                    new Aliyun_Log_Models_LogItem(null, $contents),
                ];
            } elseif ($depth == 2) {
                $logItems = [];
                foreach ($contents as $content) {
                    if (ArrayHelper::getDepth($content) === 1) {
                        $logItems[] = new Aliyun_Log_Models_LogItem(null, $content);
                    } else {
                        throw new \Exception('Warning: Content Invalid');
                    }
                }
            } else {
                throw new \RuntimeException('Warning: Content Invalid.');
            }
            $putLogsRequest = new Aliyun_Log_Models_PutLogsRequest($this->getProject(), $this->getLogStore(), $this->getTopic(), $this->getSource(), $logItems);
            return $this->client->putLogs($putLogsRequest);
        } catch (\Aliyun\SLS\Exception $e) {
            Log::channel($this->errorLogChannel)->error($e->getMessage(), $contents);
            return false;
        } catch (\Exception $e) {//暂时不抛出异常
            Log::channel($this->errorLogChannel)->error($e->getMessage(), $contents);
            return false;
        }
    }


    public function loadConfig(string $store = null)
    {
        !is_null($store) && $this->setStore($store);
        $customer = Arr::get(static::$config, 'stores.' . $this->getStore());
        if ($customer) {
            $this->accessKeyId = Arr::get($customer, 'accessKeyId');
            $this->accessKeySecret = Arr::get($customer, 'accessKeySecret');
            $this->endpoint = Arr::get($customer, 'endpoint');
            $this->project = Arr::get($customer, 'project');
            $this->logStore = Arr::get($customer, 'logStore');
            $this->topic = Arr::get($customer, 'topic');
            $this->source = Arr::get($customer, 'source');
            $errorLogChannel = Arr::get(static::$config, 'errorlogChannel');
            $errorLogChannel && $this->errorLogChannel = $errorLogChannel;

            if (empty($this->getAccessKeyId()) || empty($this->getAccessKeySecret())) {
                throw new SLSException('Warning: accessKeyId, accessKeySecret cannot be empty.');
            }
        } else {
            throw new SLSException('The store[' . $this->getStore() . '] is not found.');
        }
        return $this;
    }

    public function getAccessKeyId()
    {
        return $this->accessKeyId;
    }


    public function getAccessKeySecret()
    {
        return $this->accessKeySecret;
    }

    public function setStore(string $store)
    {
        $this->store = $store;
        return $this;
    }

    public function getStore()
    {
        return $this->store;
    }


    public function setTopic(string $topic)
    {
        $this->topic = $topic;
        return $this;
    }


    public function getTopic()
    {
        return $this->topic;
    }

    public function setSource(string $source)
    {
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setProject(string $project)
    {
        $this->project = $project;
        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setLogStore(string $logStore)
    {
        $this->logStore = $logStore;
        return $this;
    }


    public function getLogStore()
    {
        return $this->logStore;
    }
}
