<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

/**
 * Class Page.
 *
 * @api
 */
class Page extends Base
{
    /**
     * @var \Magento\Framework\View\Page\Title
     */
    protected $pageTitle;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param array $data
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Framework\View\Page\Title $pageTitle
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        array $data,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\View\Page\Title $pageTitle
    ) {
        parent::__construct($context, $helper, $data);
        $this->pageTitle = $pageTitle;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * Datalayer for page.
     *
     * @return array
     */
    public function datalayerPage()
    {
        $title = $this->pageTitle();
        $type = $this->pageType();
        $crumbs = $this->getBreadCrumbPath();

        return $this->helper->pageLoadedPushData($title, $type, $crumbs);
    }

    /**
     * Json Datalayer for Page.
     *
     * @return string
     */
    public function datalayerPageJson()
    {
        return $this->helper->jsonify($this->datalayerPage());
    }

    /**
     * Add info to log.
     *
     * @param mixed $msg
     */
    public function log($msg)
    {
        $this->_logger->addInfo($msg);
    }

    /**
     * Get breadcrumbs path.
     *
     * @return array
     */
    protected function getBreadCrumbPath()
    {
        $titleArray = [];
        $breadCrumbs = $this->catalogHelper->getBreadcrumbPath();

        foreach ($breadCrumbs as $breadCrumb) {
            $titleArray[] = $breadCrumb['label'];
        }

        return $titleArray;
    }

    /**
     * Get short page title.
     *
     * @return mixed
     */
    protected function pageTitle()
    {
        return $this->pageTitle->getShort();
    }

    /**
     * Get page type.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    protected function pageType()
    {
        return $this->_request->getFullActionName();
    }
}
