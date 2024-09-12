<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Block\Adminhtml\Posts\Edit;

class RelatedProducts extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'products/assign_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tabs\Product
     */
    protected $blockGrid;

    /**
     * @var \Hexamarvel\Blog\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Hexamarvel\Blog\Model\PostFactory $postFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hexamarvel\Blog\Model\PostFactory $postFactory,
        array $data = []
    ) {
        $this->_postFactory = $postFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Hexamarvel\Blog\Block\Adminhtml\Posts\Edit\Tab\AssignProductsToPost::class,
                'posts.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return jSon
     */
    public function getProductsJson()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        if (!empty($id)) {
            $blog = $this->_postFactory->create()->load($id);

            if (!empty($blog->getRelatedProducts())) {
                return $blog->getRelatedProducts();
            }
        }

        return '{}';
    }
}
