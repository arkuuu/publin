<?php

namespace arkuuu\Publin\Indices\Other;

use arkuuu\Publin\Indices\Exceptions\IndexException;

/**
 * This class represents the normalization factors for the 21
 * isi fields of study as defined by Iglesias and Pecharromán
 * in this publication:
 * http://dx.doi.org/10.1007/s11192-007-1805-x
 *
 * The indices may use this class to correct the influence of
 * the field of study on the amount of received citations.
 *
 * @package arkuuu\Publin\Indices\Other
 */
class NormalizationFactors
{

    /**
     * The variable contains an array with the names of the
     * fields of study as keys and the normalization factors
     * as values.
     *
     * @var array
     */
    private static $normalizationFactors = array(
        'Agricultural Sciences'        => 1.27,
        'Biology & Biochemistry'       => 0.60,
        'Chemistry'                    => 0.92,
        'Clinical Medicine'            => 0.76,
        'Computer Science'             => 1.75,
        'Economics & Business'         => 1.32,
        'Engineering'                  => 1.70,
        'Environment/Ecology'          => 0.88,
        'Geosciences'                  => 0.88,
        'Immunology'                   => 0.52,
        'Materials Science'            => 1.36,
        'Mathematics'                  => 1.83,
        'Microbiology'                 => 0.63,
        'Molecular Biology & Genetics' => 0.44,
        'Neuroscience & Behavior'      => 0.56,
        'Pharmacology & Toxicology'    => 0.84,
        'Physics'                      => 1.00,
        'Plant & Animal Science'       => 1.08,
        'Psychiatry/Psychology'        => 0.88,
        'Social Sciences, general'     => 1.60,
        'Space Science'                => 0.74,
    );


    /**
     * Returns the normalization factor for a specific
     * field of study.
     *
     * @param string $fieldOfStudy The case-sensitive name of
     *                             the field of study.
     *
     * @return float The normalization factor for the field of study.
     *
     * @throws IndexException If no field of study exists under the
     * name $fieldOfStudy.
     */
    public static function getFactorFor($fieldOfStudy)
    {
        if (array_key_exists($fieldOfStudy, self::$normalizationFactors)) {
            return self::$normalizationFactors[$fieldOfStudy];
        } else {
            throw new IndexException('The field of study with the name
                '.$fieldOfStudy.' doesn\'t exist.');
        }
    }
}
