<?php

namespace RexShijaku\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  update
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UpdateExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $criterion_ = new CriterionExtractor($this->options);

        $records = array(); // collect data so you gave only records and know about if is it batch or not

        foreach ($parsed['SET'] as $key => $item) {
            if ($item['expr_type'] == 'expression') {
                $curr_index = 0;
                foreach ($item['sub_tree'] as $index => $inner) {

                    if ($index < $curr_index)
                        continue; // skip those collected in inner loop

                    if (in_array($inner['expr_type'], array('operator', 'reserved'))) {

                        $left = $criterion_->getLeft($index, $item['sub_tree']);
                        $right = $criterion_->getRight($index, $item['sub_tree'], $curr_index);
                        $records[] = array('field' => $left['value'], 'value' => $right['value'], 'raw_val' => $right['is_raw']);
                    }
                }

            }

        }
        return array('records' => $records, 'is_batch' => false);
    }
}