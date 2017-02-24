<?php

namespace arkuuu\Publin\Indices\Implementations;

use arkuuu\Publin\Database;

/**
 * This class implements the tapered h-index as defined
 * by Anderson, Hankin and Killworth in this publication:
 * http://dx.doi.org/10.1007/s11192-007-2071-2
 *
 * @package arkuuu\Publin\Indices\Implementations
 */
class TaperedHIndex extends HIndex
{

    /**
     * {@inheritDoc}
     */
    public function __construct(Database $db)
    {
        parent::__construct($db);

        $this->name = 'tapered h-index';
    }


    /**
     * {@inheritDoc}
     */
    protected function calculateValue()
    {
        $value = 0;

        $j = 1;
        foreach ($this->data['publications'] as $publication) {
            $citationCount = $publication['citationCount'];
            if ($citationCount <= $j) {
                $value += $citationCount / (2 * $j - 1);
            } else {
                $value += $j / (2 * $j - 1);
                for ($i = $j + 1; $i <= $citationCount; $i++) {
                    $value += 1 / (2 * $i - 1);
                }
            }

            $j++;
        }

        $this->value = round($value, 2);
    }
}
