<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Category;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Hexamarvel\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$data) {
            $this->_redirect('*/*/index');
            return;
        }
        $category = $this->categoryFactory->create()->load(
            $data['category_information']['category_identifier'],
            'category_identifier'
        );
        $alreadyAvailable = false;
        if (isset($data['category_information']['id'])) {
            if ($category->getId() && $category->getId() != $data['category_information']['id']) {
                $alreadyAvailable = true;
            }
        } elseif ($category->getId()) {
            $alreadyAvailable = true;
        }

        if ($alreadyAvailable) {
            $this->messageManager->addError(
                __(
                    'Category Identifier must be unique. "%1" is already available.',
                    $data['category_information']['category_identifier']
                )
            );
            if (isset($data['category_information']['id'])) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $data['category_information']['id']]);
            }

            return $resultRedirect->setPath('*/*/edit');
        }

        try {
            $rowData = $this->categoryFactory->create();
            $postData = $data['category_information'];
            $rowData->setData($postData);

            if (isset($data['category_information']['id'])) {
                $rowData->setId($data['category_information']['id']);
            }

            $rowData->save();
            $this->messageManager->addSuccess(__('Category has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $rowData->getId()]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
