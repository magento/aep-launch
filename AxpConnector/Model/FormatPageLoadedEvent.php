<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Model;

/**
 * Page loaded event
 * @deprecated
 */
class FormatPageLoadedEvent
{
    /**
     * Push data when page is loaded (?)
     *
     * @param string $pageTitle
     * @param string $pageType
     * @param array $breadcrumbs
     * @return array
     * @depracated This method is only temporarily used as a part of refactoring routine.
     */
    public function execute($pageTitle, $pageType, $breadcrumbs): array
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
