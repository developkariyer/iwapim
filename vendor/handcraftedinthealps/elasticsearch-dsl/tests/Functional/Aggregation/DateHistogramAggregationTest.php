<?php
/**
 * @since     Feb 2022
 * @author    Haydar KULEKCI <haydarkulekci@gmail.com>
 */

namespace ONGR\ElasticsearchDSL\Tests\Functional\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Tests\Functional\AbstractElasticsearchTestCase;

class DateHistogramAggregationTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray(): array
    {
        return [
            'products' => [
                [
                    'title' => 'acme',
                    'price' => 10,
                    'created_at' => '2022-01-01T00:02:00Z',
                ],
                [
                    'title' => 'foo',
                    'price' => 20,
                    'created_at' => '2022-01-01T00:01:00Z',
                ],
                [
                    'title' => 'bar',
                    'price' => 10,
                    'created_at' => '2022-01-01T00:03:00Z',
                ],
            ]
        ];
    }

    /**
     * Match all test
     */
    public function testDateHistogramWithMinuteCalendarInterval(): void
    {
        $histogram = new DateHistogramAggregation('dates', 'created_at');
        $histogram->setCalendarInterval('minute');

        $search = new Search();
        $search->addAggregation($histogram);
        $results = $this->executeSearch($search, true);
        $this->assertCount(count($this->getDataArray()['products']), $results['aggregations']['dates']['buckets']);
    }

    /**
     * Match all test
     */
    public function testDateHistogramWithMonthCalendarInterval(): void
    {
        $histogram = new DateHistogramAggregation('dates', 'created_at');
        $histogram->setCalendarInterval('month');

        $search = new Search();
        $search->addAggregation($histogram);
        $results = $this->executeSearch($search, true);
        $this->assertCount(1, $results['aggregations']['dates']['buckets']);
    }

    /**
     * Match all test
     */
    public function testDateHistogramWitMinuteFixedInterval(): void
    {
        $histogram = new DateHistogramAggregation('dates', 'created_at');
        $histogram->setFixedInterval('2m');

        $search = new Search();
        $search->addAggregation($histogram);
        $results = $this->executeSearch($search, true);
        $this->assertCount(2, $results['aggregations']['dates']['buckets']);
    }
}
