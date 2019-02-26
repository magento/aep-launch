<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchTheme\Model;

/**
 * Keeps track of the breadcrumbs rendered on the page.
 */
class RenderedBreadcrumbs
{
    /**
     * @var array
     */
    private $breadcrumbs = [];

    /**
     * Append a rendered breadcrumb to be displayed in the datalayer. Following contract of the Breadcrumbs block.
     *
     * @param string $crumbName
     * @param array $crumbInfo
     * @return void
     */
    public function appendBreadcrumb(string $crumbName, array $crumbInfo): void
    {
        if (!isset($this->breadcrumbs[$crumbName]) || !isset($this->breadcrumbs[$crumbName]['readonly'])) {
            $this->breadcrumbs[$crumbName] = $crumbInfo;
        }
    }

    /**
     * Get list of all breadcrumbs rendered for the specific page.
     *
     * @return array
     */
    public function getAllRenderedBreadcrumbs(): array
    {
        return array_column($this->breadcrumbs, 'label');
    }
}
