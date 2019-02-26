<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchTheme\Plugin;

use Magento\Theme\Block\Html\Breadcrumbs as BreadcrumbsBlock;
use Adobe\LaunchTheme\Model\RenderedBreadcrumbs;

/**
 * Track page breadcrumbs to add to the datalayer.
 */
class Breadcrumbs
{
    /**
     * @var RenderedBreadcrumbs
     */
    private $renderedBreadcrumbs;

    /**
     * @param RenderedBreadcrumbs $renderedBreadcrumbs
     */
    public function __construct(RenderedBreadcrumbs $renderedBreadcrumbs)
    {
        $this->renderedBreadcrumbs = $renderedBreadcrumbs;
    }

    /**
     * "Catch" breadcrumbs added to the block for further processing.
     *
     * @param BreadcrumbsBlock $subject
     * @param string $crumbName
     * @param array $crumbInfo
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAddCrumb(BreadcrumbsBlock $subject, $crumbName, $crumbInfo)
    {
        $this->renderedBreadcrumbs->appendBreadcrumb($crumbName, $crumbInfo);
    }
}
