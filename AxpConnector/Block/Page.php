<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Magento\Framework\View\Element\Template;
use Adobe\AxpConnector\Model\Datalayer;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Page.
 *
 * @api
 */
class Page extends Template
{
    /**
     * @var \Magento\Framework\View\Page\Title
     */
    private $pageTitle;

    /**
     * @var \Magento\Catalog\Helper\Data
     * @deprecated Public APIs should be used instead of helpers where possible.
     */
    private $catalogHelper;

    /**
     * @var Datalayer
     */
    private $datalayer;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Framework\View\Page\Title $pageTitle
     * @param Datalayer $datalayer
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\View\Page\Title $pageTitle,
        Datalayer $datalayer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTitle = $pageTitle;
        $this->catalogHelper = $catalogHelper;
        $this->datalayer = $datalayer;
    }

    /**
     * Datalayer for page.
     *
     * @return string
     * @deprecated Due to redundancy
     */
    private function datalayerPage(): string
    {
        $title = $this->pageTitle();
        $type = $this->pageType();
        $crumbs = $this->getBreadCrumbPath();

        return $this->datalayer->pageLoadedPushData($title, $type, $crumbs);
    }

    /**
     * Json Datalayer for Page.
     *
     * @return string
     */
    public function datalayerPageJson(): string
    {
        return $this->datalayerPage();
    }

    /**
     * Get breadcrumbs path.
     *
     * @return array
     * @deprecated Public APIs should be used instead of helpers where possible.
     */
    private function getBreadCrumbPath(): array
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
     * @return string
     */
    private function pageTitle(): string
    {
        return $this->pageTitle->getShort();
    }

    /**
     * Get page type.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @deprecated Request should not be used directly.
     */
    private function pageType()
    {
        return $this->getRequest()->getFullActionName();
    }
}
