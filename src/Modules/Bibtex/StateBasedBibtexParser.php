<?php
namespace arkuuu\Publin\Modules\Bibtex;

/* modified part (such that the generic bibtex parser can be used directly on strings) of
  bibtexbrowser: publication lists with bibtex and PHP
<!--this is version v20140918 -->
URL: http://www.monperrus.net/martin/bibtexbrowser/
Feedback & Bug Reports: martin.monperrus@gmail.com

(C) 2013 Matthieu Guillaumin
(C) 2006-2013 Martin Monperrus
(C) 2005-2006 The University of Texas at El Paso / Joel Garcia, Leonardo Ruiz, and Yoonsik Cheon
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

*/

/** is a generic parser of bibtex files.
 * usage:
 * <pre>
 * $delegate = new XMLPrettyPrinter();// or another delegate such as BibDBBuilder
 * $parser = new StateBasedBibtexParser($delegate);
 * $parser->parse('foo.bib');
 * </pre>
 * notes:
 * - It has no dependencies, it can be used outside of bibtexbrowser
 * - The delegate is expected to have some methods, see classes BibDBBuilder and XMLPrettyPrinter
 */
class StateBasedBibtexParser
{

    var $delegate;


    function __construct(ArrayBuilder $delegate)
    {
        $this->delegate = $delegate;
    }


    function parse($bibtexcontent)
    {
        $delegate = $this->delegate;
        // STATE DEFINITIONS
        @define('NOTHING', 1);
        @define('GETTYPE', 2);
        @define('GETKEY', 3);
        @define('GETVALUE', 4);
        @define('GETVALUEDELIMITEDBYQUOTES', 5);
        @define('GETVALUEDELIMITEDBYQUOTES_ESCAPED', 6);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS', 7);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED', 8);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL', 9);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED', 10);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL', 11);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED', 12);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL', 13);
        @define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED', 14);

        $state = NOTHING;
        $entrytype = '';
        $entrykey = '';
        $entryvalue = '';
        $finalkey = '';
        $entrysource = '';

        // metastate
        $isinentry = false;

        $delegate->beginFile();

        for ($i = 0; $i < strlen($bibtexcontent); $i++) {
            $s = $bibtexcontent[$i];

            if ($isinentry) {
                $entrysource .= $s;
            }

            if ($state == NOTHING) {
                // this is the beginning of an entry
                if ($s == '@') {
                    $delegate->beginEntry();
                    $state = GETTYPE;
                    $isinentry = true;
                    $entrysource = '@';
                }
            } else if ($state == GETTYPE) {
                // this is the beginning of a key
                if ($s == '{') {
                    $state = GETKEY;
                    $delegate->setEntryType($entrytype);
                    $entrytype = '';
                } else {
                    $entrytype = $entrytype.$s;
                }
            } else if ($state == GETKEY) {
                // now we get the value
                if ($s == '=') {
                    $state = GETVALUE;
                    $finalkey = $entrykey;
                    $entrykey = '';
                } // oups we only have the key :-) anyway
                else if ($s == '}') {
                    $state = NOTHING;
                    $isinentry = false;
                    $delegate->endEntry($entrysource);
                    $entrykey = '';
                } // OK now we look for values
                else if ($s == ',') {
                    $state = GETKEY;
                    $delegate->setEntryKey($entrykey);
                    $entrykey = '';
                } else {
                    $entrykey = $entrykey.$s;
                }
            }
            // we just got a =, we can now receive the value, but we don't now whether the value
            // is delimited by curly brackets, double quotes or nothing
            else if ($state == GETVALUE) {

                // the value is delimited by double quotes
                if ($s == '"') {
                    $state = GETVALUEDELIMITEDBYQUOTES;
                } // the value is delimited by curly brackets
                else if ($s == '{') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
                } // the end of the key and no value found: it is the bibtex key e.g. \cite{Descartes1637}
                else if ($s == ',') {
                    $state = GETKEY;
                    $delegate->setEntryField(trim($finalkey), $entryvalue);
                    $entryvalue = ''; // resetting the value buffer
                } // this is the end of the value AND of the entry
                else if ($s == '}') {
                    $state = NOTHING;
                    $delegate->setEntryField(trim($finalkey), $entryvalue);
                    $isinentry = false;
                    $delegate->endEntry($entrysource);
                    $entryvalue = ''; // resetting the value buffer
                } else if ($s == ' ' || $s == "\t" || $s == "\n" || $s == "\r") {
                    // blank characters are not taken into account when values are not in quotes or curly brackets
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } /* GETVALUEDELIMITEDBYCURLYBRACKETS* handle entries delimited by curly brackets and the possible nested curly brackets */
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS) {

                if ($s == '\\') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '{') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '}') {
                    $state = GETVALUE;
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } // handle anti-slashed brackets
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED) {
                $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
                $entryvalue = $entryvalue.$s;
            } // in first level of curly bracket
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL) {
                if ($s == '\\') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '{') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '}') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
                    $entryvalue = $entryvalue.$s;
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } // handle anti-slashed brackets
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED) {
                $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;
                $entryvalue = $entryvalue.$s;
            } // in second level of curly bracket
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL) {
                if ($s == '\\') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '{') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '}') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;
                    $entryvalue = $entryvalue.$s;
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } // handle anti-slashed brackets
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED) {
                $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;
                $entryvalue = $entryvalue.$s;
            } // in third level of curly bracket
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL) {
                if ($s == '\\') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '}') {
                    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;
                    $entryvalue = $entryvalue.$s;
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } // handle anti-slashed brackets
            else if ($state == GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED) {
                $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;
                $entryvalue = $entryvalue.$s;
            } /* handles entries delimited by double quotes */
            else if ($state == GETVALUEDELIMITEDBYQUOTES) {

                if ($s == '\\') {
                    $state = GETVALUEDELIMITEDBYQUOTES_ESCAPED;
                    $entryvalue = $entryvalue.$s;
                } else if ($s == '"') {
                    $state = GETVALUE;
                } else {
                    $entryvalue = $entryvalue.$s;
                }
            } // handle anti-double quotes
            else if ($state == GETVALUEDELIMITEDBYQUOTES_ESCAPED) {
                $state = GETVALUEDELIMITEDBYQUOTES;
                $entryvalue = $entryvalue.$s;
            }
        } // end for
        $delegate->endFile();
        //$d = &$this->delegate;print_r($d);
    } // end function
} // end class
?>
