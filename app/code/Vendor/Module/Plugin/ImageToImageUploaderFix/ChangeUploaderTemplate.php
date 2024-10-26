<?php

namespace Vendor\Module\Plugin\ImageToImageUploaderFix;

class ChangeUploaderTemplate
{
    /**
     * Перехват шаблона и замена на существующий uploader
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\ModifierPool $subject
     * @param array $result
     * @return array
     */
    public function afterModifyData(
        \Magento\Framework\View\Element\UiComponent\DataProvider\ModifierPool $subject,
        $result
    ) {
        foreach ($result as $key => $value) {
            if (isset($value['template']) && $value['template'] === 'ui/form/element/uploader/image') {
                $result[$key]['template'] = 'ui/form/element/image-uploader';
            }
        }
        return $result;
    }
}
