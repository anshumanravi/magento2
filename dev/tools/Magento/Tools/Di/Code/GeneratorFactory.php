<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\Di\Code;

use Magento\Framework\ObjectManagerInterface;

class GeneratorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Creates operation
     *
     * @param array $arguments
     * @return Generator
     */
    public function create($arguments = [])
    {
        return $this->objectManager->create('Magento\Tools\Di\Code\Generator', $arguments);
    }
}
