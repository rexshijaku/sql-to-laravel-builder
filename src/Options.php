<?php

namespace RexShijaku\SQLToLaravelBuilder;

class Options
{
    private $options;
    private $aggregate_fn = array('sum', 'min', 'max', 'avg', 'sum', 'count');
    private $defaults = array(
        'facade' => 'DB::',
        'group' => true
    );
    private $supporting_fn = array('date', 'month', 'year' ,'day', 'time');

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function set(): void
    {
        if (!is_array($this->options))
            $this->options = array();

        foreach ($this->defaults as $k => $v)
            if (!key_exists($k, $this->options))
                $this->options[$k] = $v;
            else {
                if (gettype($this->options[$k]) != gettype($this->defaults[$k]))
                    throw new \Exception('Invalid type in options. [' . $k . ' param type must be ' . gettype($this->defaults[$k]) . ']');
            }

        unset($this->options['settings']); // unset reserved
        $this->options['settings']['agg'] = $this->aggregate_fn;
        $this->options['settings']['fns'] = $this->supporting_fn;
    }

    public function get()
    {
        return $this->options;
    }

}