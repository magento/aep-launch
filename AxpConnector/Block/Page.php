<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Block;

use Adobe\AxpConnector\Api\AddDatalayerEventInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Adobe\AxpConnector\Model\FormatPageLoadedEvent;

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
     * @var FormatPageLoadedEvent
     */
    private $formatPageLoadedEvent;

    /**
     * @var AddDatalayerEventInterface
     */
    private $addDatalayerEvent;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Framework\View\Page\Title $pageTitle
     * @param FormatPageLoadedEvent $formatPageLoadedEvent
     * @param AddDatalayerEventInterface $addDatalayerEvent
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\View\Page\Title $pageTitle,
        FormatPageLoadedEvent $formatPageLoadedEvent,
        AddDatalayerEventInterface $addDatalayerEvent,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTitle = $pageTitle;
        $this->catalogHelper = $catalogHelper;
        $this->formatPageLoadedEvent = $formatPageLoadedEvent;
        $this->addDatalayerEvent = $addDatalayerEvent;
    }

    /**
     * Json Datalayer for Page.
     *
     * @return string
     * @deprecated
     */
    public function toHtml()
    {
        $result = parent::toHtml();

        $title = $this->pageTitle();
        $type = $this->pageType();
        $crumbs = $this->getBreadCrumbPath();

        $eventData = $this->formatPageLoadedEvent->execute($title, $type, $crumbs);
        $this->addDatalayerEvent->execute($eventData);

        return $result;
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
