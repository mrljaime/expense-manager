<?php

namespace App\Model\Expression;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

/**
 * \App\Model\Expression\ExpressionLanguage.
 */
class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct(?CacheItemPoolInterface $cache = null, iterable $providers = [])
    {
        parent::__construct($cache, $providers);
    }
}
