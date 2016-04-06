<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Module\I18n\Parser\Adapter;

use Magento\Email\Model\Template\Filter;

/**
 * Html parser adapter
 */
class Html extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function _parse()
    {
        $data = file_get_contents($this->_file);
        if ($data === false) {
            throw new \Exception('Failed to load file from disk.');
        }

        $results = [];
        preg_match_all(Filter::CONSTRUCTION_PATTERN, $data, $results, PREG_SET_ORDER);
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i][1] === Filter::TRANS_DIRECTIVE_NAME) {
                $directive = [];
                if (preg_match(Filter::TRANS_DIRECTIVE_REGEX, $results[$i][2], $directive) !== 1) {
                    continue;
                }
                $quote = $directive[1];
                $this->_addPhrase($quote . $directive[2] . $quote);
            }
        }
    }
}
