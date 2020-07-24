<?php
namespace Ketwaroo\Text;

/**
 * Unaccent
 */
class Unaccent {

    use \Ketwaroo\Pattern\TraitSingleton;

    protected $charMapSearch;
    protected $charMapReplace;

    public function __construct() {

        $reg = [
            // a-z
            '~^&([a-z])(?:acute|apos|breve|caron|cedil|cedli|circ|cy|dblac|dot|green|grave|macr|midot|modot|nodot|opf|ogon|ring|fr|scr|slash|strok|tilde|uml);$~i' => '\1',
            '~^&([a-z]+?)(?:lig|cy);$~i' => '\1',
            '~^&(eth|eng);$~i' => '\1',
            '~^&([^;]+);$~i' => '\1',
        ];

        $chars = array_diff(
                preg_replace(
                        array_keys($reg),
                        array_values($reg),
                        get_html_translation_table(HTML_ENTITIES, ENT_HTML5 | ENT_NOQUOTES)
                ),
// remove safe chars
                [
                    'Tab',
                    'NewLine',
                    'dollar',
                    'lpar',
                    'rpar',
                    'excl',
                    'period',
                    'comma',
                    'ast',
                    'ast',
                    'hyphen',
                    'vert',
                    'commat',
                    'num',
                    'Hat',
                    'lowbar',
                    'plus',
                    'lbrace',
                    'rbrace',
                    'rcub',
                    'quest',
                    'gt',
                    'lt',
                    'sol',
                    'equals',
                    'lbrack',
                    'rsqb',
                    'semi',
                    'sol',
                    'bsol',
                    'amp',
                    'percnt',
                    'colon',
                ]
        );
        $this->charMapSearch = array_keys($chars);
        $this->charMapReplace = array_values($chars);
    }

    /**
     * Attempts to remove accents
     * @param string $input
     * @return string
     */
    public function unaccent(string $input) {
        return str_replace($this->charMapSearch, $this->charMapReplace, $input);
    }

}
