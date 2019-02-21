<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalog\Model;

/**
 * Format Category Viewed datalayer event data.
 */
class FormatCategoryViewedEvent
{
    /**
     * Format Category Viewed datalayer event data.
     *
     * @param int $resultsShown
     * @param int $resultsCount
     * @param string $listOrder
     * @param string $sortDirection
     * @return array
     */
    public function execute(int $resultsShown, int $resultsCount, string $listOrder, string $sortDirection): array
    {
        return [
            'event' => 'Listing Viewed',
            'listing' => [
                'listingResults' => [
                    'resultsShown' => $resultsShown,
                    'resultsCount' => $resultsCount
                ],
                'listingParams' => [
                    'sorts' => [
                        'sortOrder' => $sortDirection,
                        'sortKey' => $listOrder
                    ]
                ]
            ]
        ];
    }
}
