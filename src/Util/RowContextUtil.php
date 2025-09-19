<?php

namespace App\Util;

use Psr\Log\LoggerInterface;

/**
 * \App\Util\RowContextUtil.
 */
class RowContextUtil
{
    private ?array $row = null;

    public function __construct(
        private readonly array $config,
        private readonly array $headers,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function setRow(?array $row): RowContextUtil
    {
        $this->row = $row;

        return $this;
    }

    public function getValue(string $canonical): mixed
    {
        $val = null;
        $columns = $this->config['columns'];

        foreach ($columns[$canonical] as $value) {
            $item = array_filter(
                $this->headers,
                fn ($header, $index) => $header == $value,
                ARRAY_FILTER_USE_BOTH
            );

            if (!is_null($this->logger)) {
                $this->logger->debug(
                    sprintf(
                        'Item found for "%s" in %s: %s',
                        $canonical,
                        $value,
                        var_export($item, true)
                    )
                );
            }

            if (empty($item)) {
                continue;
            }

            if (!is_null($this->logger)) {
                $this->logger->debug(sprintf(
                    'Value from "%s" in row: %s', $canonical, $this->row[array_key_first($item)]
                ));
            }
            $val = $this->row[array_key_first($item)];

            break;
        }

        return $val;
    }
}
