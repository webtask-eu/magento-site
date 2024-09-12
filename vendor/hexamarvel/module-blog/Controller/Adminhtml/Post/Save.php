<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Save extends \Magento\Backend\App\Action
{
     /**
     * @var \Hexamarvel\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Hexamarvel\Blog\Helper\Data
     */
    protected $helper;

    protected $imageUploader;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Hexamarvel\Blog\Model\PostFactory $postFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hexamarvel\Blog\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->postFactory  = $postFactory;
        $this->filesystem   = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->helper       = $helper;
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

        if (empty($data['post_information']['category']) || !$data['post_information']['category'][0]) {
            $this->messageManager->addError(
                __(
                    'Category is empty. Please add some category for the posts.'
                )
            );
            if (isset($data['post_information']['id'])) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $data['post_information']['id']]);
            }
            return $resultRedirect->setPath('*/*/edit');
        }

        $post = $this->postFactory->create()->load($data['post_information']['identifier'], 'identifier');
        $alreadyAvailable = false;
        if (isset($data['post_information']['id'])) {
            if ($post->getId() && $post->getId() != $data['post_information']['id']) {
                $alreadyAvailable = true;
            }
        } elseif ($post->getId()) {
            $alreadyAvailable = true;
        }

        if ($alreadyAvailable) {
            $this->messageManager->addError(
                __(
                    'Post Identifier must be unique. "%1" is already available.',
                    $data['post_information']['identifier']
                )
            );
            if (isset($data['post_information']['id'])) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $data['post_information']['id']]);
            }
            return $resultRedirect->setPath('*/*/edit');
        }

        try {
            $rowData = $this->postFactory->create();
            $postData = [
                'title' => $data['post_information']['title'],
                'identifier' => $data['post_information']['identifier'],
                'sort_order' => $data['post_information']['sort_order'],
                'store' => implode(',', $data['post_information']['store']),
                'category' => implode(',', $data['post_information']['category']),
                'is_active' => $data['post_information']['is_active'],
                'short_content' => $data['post_information']['short_content'],
                'content' => $data['post_information']['content'],
                'meta_keywords' => $data['advanced_options']['meta_keywords'],
                'meta_description' => $data['advanced_options']['meta_description'],
                'author' => $data['advanced_options']['author'],
                'created_on' => $data['advanced_options']['created_on'],
                'related_products' => (isset($data['related_products'])) ? $data['related_products'] : '',
            ];
            if (isset($data['post_information']['blog_image'][0]['name'])) {
                if (isset($data['post_information']['blog_image'][0]['tmp_name'])) {
                    $this->imageUploader = ObjectManager::getInstance()->get('Hexamarvel\Blog\ImageUpload');
                    $this->imageUploader->moveFileFromTmp($data['post_information']['blog_image'][0]['name']);
                    $height = ($this->helper->getConfig('hexablog/blog_view/blog_height')) ?: '315';
                    $width = ($this->helper->getConfig('hexablog/blog_view/blog_width')) ?: '981';
                    $data['post_information']['blog_image'][0]['url'] = $this->imageResize(
                        $data['post_information']['blog_image'][0]['name'],
                        $height,
                        $width
                    );
                }

                unset($data['post_information']['blog_image'][0]['tmp_name']);
                $postData['blog_image'] = json_encode($data['post_information']['blog_image']);
            } else {
                $postData['blog_image'] = '';
            }

            if (isset($data['post_information']['thumbnail_image'][0]['name'])) {
                if (isset($data['post_information']['thumbnail_image'][0]['tmp_name'])) {
                    $this->imageUploader = ObjectManager::getInstance()->get('Hexamarvel\Blog\ImageUpload');
                    $this->imageUploader->moveFileFromTmp($data['post_information']['thumbnail_image'][0]['name']);
                    $height = ($this->helper->getConfig('hexablog/blog_list/blog_height')) ?: '542';
                    $width = ($this->helper->getConfig('hexablog/blog_list/blog_width')) ?: '361';
                    $data['post_information']['thumbnail_image'][0]['url'] = $this->imageResize(
                        $data['post_information']['thumbnail_image'][0]['name'],
                        $height,
                        $width
                    );
                }

                unset($data['post_information']['thumbnail_image'][0]['tmp_name']);
                $postData['thumbnail_image'] = json_encode($data['post_information']['thumbnail_image']);
            } else {
                $postData['thumbnail_image'] = '';
            }

            $rowData->setData($postData);
            if (isset($data['post_information']['id'])) {
                $rowData->setId($data['post_information']['id']);
            }

            $rowData->save();
            $this->messageManager->addSuccess(__('Post has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $rowData->getId()]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * @param string $image
     * @param int $height
     * @param int $width
     * @return string
     */
    private function imageResize($image, $height = '', $width = '')
    {
        $absolutePath = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath('hexablog/post/images/').$image;

        if (!file_exists($absolutePath)) {
            return false;
        }

        $imageResized = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath('hexablog/post/images/resized/' . $width . '/').$image;
        if (!file_exists($imageResized)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $destination = $imageResized ;
            $imageResize->save($destination);
        }

        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        ) . 'hexablog/post/images/resized/' . $width . '/' . $image;
    }
}
