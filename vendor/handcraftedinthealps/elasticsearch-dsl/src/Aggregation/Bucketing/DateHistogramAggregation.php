<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation\Bucketing;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Type\BucketingTrait;

/**
 * Class representing Histogram aggregation.
 *
 * @link https://goo.gl/hGCdDd
 */
class DateHistogramAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var string
     */
    protected $interval;

    /**
     * @var string
     */
    protected $calendarInterval;

    /**
     * @var string
     */
    protected $fixedInterval;

    /**
     * @var string
     */
    protected $format;

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $interval
     * @param string|null $format
     */
    public function __construct($name, string $field = null, string $interval = null, string $format = null)
    {
        parent::__construct($name);

        $this->setField($field);
        $this->setCalendarInterval($interval);
        $this->setFormat($format);
    }

    /**
     * @return string
     * @deprecated use getCalendarInterval instead
     */
    public function getInterval()
    {
        return $this->calendarInterval;
    }

    /**
     * @param string $interval
     * @deprecated use setCalendarInterval instead
     *
     * @return $this
     */
    public function setInterval($interval)
    {
        $this->setCalendarInterval($interval);

        return $this;
    }


    /**
     * @return string
     */
    public function getFixedInterval()
    {
        return $this->fixedInterval;
    }

    /**
     * @param string $interval
     * @return $this
     */
    public function setFixedInterval($interval)
    {
        $this->fixedInterval = $interval;

        return $this;
    }

    /**
     * @return string
     */
    public function getCalendarInterval()
    {
        return $this->calendarInterval;
    }

    /**
     * @param string $interval
     * @return $this
     */
    public function setCalendarInterval($interval)
    {
        $this->calendarInterval = $interval;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'date_histogram';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        if (!$this->getField() || !($this->getCalendarInterval() || $this->getFixedInterval())) {
            throw new \LogicException('Date histogram aggregation must have field and interval set.');
        }

        $out = [
            'field' => $this->getField(),
        ];

        if ($this->getCalendarInterval()) {
            $out['calendar_interval'] = $this->getCalendarInterval();
        } elseif ($this->getFixedInterval()) {
            $out['fixed_interval'] = $this->getFixedInterval();
        }

        if (!empty($this->format)) {
            $out['format'] = $this->format;
        }

        return $out;
    }
}
