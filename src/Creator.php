<?php

namespace RexShijaku\SQLToLaravelBuilder;

use RexShijaku\SQLToLaravelBuilder\builders\CriterionBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\DeleteBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\FromBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\GroupByBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\HavingBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\InsertBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\JoinBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\LimitBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\OrderBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\SelectBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\UnionBuilder;
use RexShijaku\SQLToLaravelBuilder\builders\UpdateBuilder;
use RexShijaku\SQLToLaravelBuilder\extractors\CriterionExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\DeleteExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\FromExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\GroupByExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\HavingExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\InsertExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\JoinExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\LimitExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\OrderExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\SelectExtractor;
use RexShijaku\SQLToLaravelBuilder\extractors\UpdateExtractor;

/**
 * This class orchestrates the process between Extractors and Builders in order to produce parts of Query Builder and arranges them
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class Creator extends AbstractCreator
{
    private $main;
    public $options;
    public $skip_bag;

    function __construct($main, $options)
    {
        $this->main = $main;
        $this->options = $options;
        $this->skip_bag = array();
    }

    public function select($value, $parsed)
    {
        $extractor = new SelectExtractor($this->options);
        $builder = new SelectBuilder($this->options);

        $parts = $extractor->extract($value, $parsed);
        $build_res = $builder->build($parts, $this->skip_bag);

        $this->qb_closed = $build_res['close_qb'];
        if ($build_res['type'] == 'eq')
            $this->qb = $build_res['query_part'];
        else if ($build_res['type'] == 'lastly')
            $this->lastly = $build_res['query_part'];
    }

    public function from($value, $parsed)
    {
        $from_extractor = new FromExtractor($this->options);
        $from_builder = new FromBuilder($this->options);

        if ($this->isSingleTable($parsed)) {
            $from_parts = $from_extractor->extractSingle($value);
            $this->qb = $from_builder->build($from_parts, $this->skip_bag) . $this->qb;
        } else { // more than one table involved ?

            $from_parts = $from_extractor->extract($value);
            if (isset($from_parts['joins'])) { // invalid joins found ?
                throw new \Exception('Invalid join type found! ');
            } else {
                $this->qb = $from_builder->build($from_parts) . $this->qb;

                $join_extractor = new JoinExtractor($this->options);
                $join_builder = new JoinBuilder($this->options);

                $joins = $join_extractor->extract($value);
                $this->qb .= $join_builder->build($joins);
            }
        }
    }

    public function where($value)
    {
        $extractor = new CriterionExtractor($this->options);
        $builder = new CriterionBuilder($this->options);

        if ($extractor->extractAsArray($value, $part))
            $q = $builder->buildAsArray($part);
        else {
            $parts = $extractor->extract($value);
            $q = $builder->build($parts);
        }
        $this->qb .= $q;
    }

    public function group_by($value)
    {
        $extractor = new GroupByExtractor($this->options);
        $builder = new GroupByBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function limit($value)
    {
        $extractor = new LimitExtractor($this->options);
        $builder = new LimitBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function having($value)
    {
        $extractor = new HavingExtractor($this->options);
        $builder = new HavingBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function order($value)
    {
        $extractor = new OrderExtractor($this->options);
        $builder = new OrderBuilder($this->options);

        $parts = $extractor->extract($value);
        $q = $builder->build($parts);
        $this->qb .= $q;
    }

    public function insert($value, $parsed)
    {
        $extractor = new InsertExtractor($this->options);
        $builder = new InsertBuilder($this->options);

        $parts = $extractor->extract($value, $parsed);
        $q = $builder->build($parts);
        $this->qb .= $q;

        unset($this->options['command']);
    }

    public function update($value, $parsed)
    {
        $extractor = new UpdateExtractor($this->options);
        $builder = new UpdateBuilder($this->options);

        $parts = $extractor->extract($value, $parsed);
        $q = $builder->build($parts, $this->skip_bag);
        $this->lastly = $q;
    }

    public function delete($parsed)
    {
        $extractor = new DeleteExtractor($this->options);
        $builder = new DeleteBuilder($this->options);
        $parts = $extractor->extract(array(), $parsed);
        $this->lastly = $builder->build($parts, $this->skip_bag);
    }

    public function union($parts)
    {
        $builder = new UnionBuilder($this->options);
        $this->qb = $builder->build($parts);
    }

    function getQuery($sql)
    {
        $this->qb .= $this->lastly;
        if (empty($this->qb)) {
            $this->qb .= $this->options['facade'] . "statement('" . $sql . "')";
        } else {
            if (!$this->qb_closed)
                $this->qb .= $this->in_union ? '' : '->get()';
        }

        if (!$this->in_union)
            $this->qb .= ';';
        return $this->qb;
    }

}