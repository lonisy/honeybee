<?php

namespace Honeybee\Infrastructure\DataAccess\Storage;

use Honeybee\Infrastructure\DataAccess\Connector\ConnectorInterface;
use Honeybee\Infrastructure\DataAccess\Connector\ConnectableInterface;
use Honeybee\Infrastructure\Config\ConfigInterface;
use Honeybee\Infrastructure\Config\ConfigurableInterface;
use Trellis\Common\Object;
use Psr\Log\LoggerInterface;

abstract class Storage extends Object implements ConnectableInterface, ConfigurableInterface
{
    const DOMAIN_FIELD_ID = 'identifier';

    const DOMAIN_FIELD_VERSION = 'revision';

    protected $connector;

    protected $config;

    protected $logger;

    public function __construct(ConnectorInterface $connector, ConfigInterface $config, LoggerInterface $logger)
    {
        $this->connector = $connector;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getConnector()
    {
        return $this->connector;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
