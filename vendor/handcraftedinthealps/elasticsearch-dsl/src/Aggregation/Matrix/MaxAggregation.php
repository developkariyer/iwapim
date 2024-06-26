<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation\Matrix;

/**
 * Class representing Max Aggregation Stats.
 *
 * @deprecated This class is deprecated use "MaxAggregationStats" class instead.
 */
class MaxAggregation extends MaxAggregationStats
{
    public function __construct($name, $field, $missing = null, $mode = null)
    {
        @trigger_error(
            'The class "MaxAggregation" is deprecated use "MaxAggregationStats" instead.',
            E_USER_DEPRECATED
        );

        parent::__construct($name, $field, $missing, $mode);
    }
}
