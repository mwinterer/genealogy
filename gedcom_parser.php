<?php

class GedcomParser
{
    protected $_file = null;

    public function parse($file)
    {
        $this->_file = file($file, FILE_IGNORE_NEW_LINES);
        if (!$this->_file) {
            return false;
        }

        $gedcom = [];
        $gedcom['individuals'] = [];
        $gedcom['families'] = [];

        $current_individual = null;
        $current_family = null;

        foreach ($this->_file as $line) {
            $line = trim($line);
            $parts = explode(' ', $line, 3);

            if (count($parts) < 2) {
                continue;
            }

            $level = (int)$parts[0];
            $tag = strtoupper($parts[1]);
            $value = isset($parts[2]) ? $parts[2] : '';

            if ($level === 0) {
                if (strpos($tag, '@F') === 0) {
                    $current_family = $value;
                    $gedcom['families'][$current_family] = ['id' => $current_family, 'children' => []];
                    $current_individual = null;
                } elseif (strpos($tag, '@I') === 0) {
                    $current_individual = $value;
                    $gedcom['individuals'][$current_individual] = ['id' => $current_individual, 'fams' => [], 'famc' => null];
                    $current_family = null;
                }
            } elseif ($level === 1) {
                if ($current_individual) {
                    switch ($tag) {
                        case 'NAME':
                            $gedcom['individuals'][$current_individual]['name'] = $value;
                            break;
                        case 'SEX':
                            $gedcom['individuals'][$current_individual]['sex'] = $value;
                            break;
                        case 'BIRT':
                            $gedcom['individuals'][$current_individual]['birth'] = [];
                            break;
                        case 'DEAT':
                            $gedcom['individuals'][$current_individual]['death'] = [];
                            break;
                        case 'FAMS':
                            $gedcom['individuals'][$current_individual]['fams'][] = $value;
                            break;
                        case 'FAMC':
                            $gedcom['individuals'][$current_individual]['famc'] = $value;
                            break;
                    }
                } elseif ($current_family) {
                    switch ($tag) {
                        case 'HUSB':
                            $gedcom['families'][$current_family]['husband'] = $value;
                            break;
                        case 'WIFE':
                            $gedcom['families'][$current_family]['wife'] = $value;
                            break;
                        case 'CHIL':
                            $gedcom['families'][$current_family]['children'][] = $value;
                            break;
                    }
                }
            }
        }
        return $gedcom;
    }
}
