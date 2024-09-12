<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Ui\Component\Listing\Column\Options;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * @var string DISABLED_VALUE
     */
    const DISABLED_VALUE = '0';

    /**
     * @var string ENABLED_VALUE
     */
    const ENABLED_VALUE = '1';

    /**
     * @var array currentOptions
     */
    protected $currentOptions;

    /**
     * @var array options
    */
    protected $options;


    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        $this->currentOptions = [
            'Default' => [
                'label' => __(' '),
                'value' => '-1',
            ],
            'Disabled' => [
                'label' => __('Disabled'),
                'value' => self::DISABLED_VALUE,
            ],
            'Enabled' => [
                'label' => __('Enabled'),
                'value' => self::ENABLED_VALUE,
            ],
        ];

        $this->options = array_values($this->currentOptions);
        return $this->options;
    }
}
