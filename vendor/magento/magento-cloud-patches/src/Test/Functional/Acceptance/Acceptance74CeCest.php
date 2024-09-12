<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CloudPatches\Test\Functional\Acceptance;

/**
 * @group php74ce
 */
class Acceptance74CeCest extends Acceptance74Cest
{
    /**
     * @var string
     */
    protected $edition = 'CE';

    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0'],
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0-p1'],
            ['templateVersion' => '2.4.1', 'magentoVersion' => '2.4.1'],
            ['templateVersion' => '2.4.1', 'magentoVersion' => '2.4.1-p1'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2-p1'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2-p2'],
            //  Magento 2.4.3 CE does not work on the cloud
            //  ['templateVersion' => '2.4.3', 'magentoVersion' => '2.4.3'],
            //  ['templateVersion' => '2.4.3', 'magentoVersion' => '2.4.3-p1'],
            //  ['templateVersion' => '2.4.3', 'magentoVersion' => '2.4.3-p2'],
        ];
    }
}
