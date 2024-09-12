<?php

namespace WebDev\LatvijasPasts\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use WebDev\LatvijasPasts\Model\Carrier;

class Method implements OptionSourceInterface
{
    private string $code = 'method';
    private Carrier $carrier;

    public function __construct(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    public function toOptionArray()
    {
        $configData = $this->carrier->getCode($this->code);
        $arr = [];
        foreach ($configData as $code => $title) {
            $arr[] = ['value' => $code, 'label' => $title];
        }
        return $arr;
    }
}
