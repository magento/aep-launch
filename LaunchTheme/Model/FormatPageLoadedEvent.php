<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchTheme\Model;

/**
 * Page loaded event data formatter.
 */
class FormatPageLoadedEvent
{
    /**
     * Format data for the Page Loaded event.
     *
     * @param string $pageTitle
     * @param string $pageType
     * @param array $breadcrumbs
     * @return array
     */
    public function execute(string $pageTitle, string $pageType, array $breadcrumbs = []): array
    {
        return [
            'event' => 'Page Loaded',
            'page' => [
                'pageType' => $pageType,
                'pageName' => $pageTitle,
                'breadcrumbs' => $breadcrumbs
            ]
        ];
    }
}
