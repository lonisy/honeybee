<?php

namespace Honeybee\Infrastructure\DataAccess\Connector\Flysystem;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Config;

/**
 * Flysystem Local adapter doesn't use 'b' as mode on fopen calls.
 */
class LocalAdapter extends Local
{
    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $location = $this->applyPathPrefix($path);
        $stream = fopen($location, 'rb');

        return compact('stream', 'path');
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        $stream = fopen($location, 'wb+');
        if ($stream === false) {
            return false;
        }

        stream_copy_to_stream($resource, $stream);

        if (!fclose($stream)) {
            return false;
        }

        if ($visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }

        return compact('path', 'visibility');
    }
}
