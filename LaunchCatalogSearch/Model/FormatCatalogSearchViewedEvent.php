<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchCatalogSearch\Model;

/**
 * Format Category Viewed datalayer event data.
 */
class FormatCatalogSearchViewedEvent
{
    /**
     * Format Catalog Search Viewed datalayer event data.
     *
     * @param string $searchQuery
     * @return array
     */
    public function execute(string $searchQuery): array
    {
        return [
            'listing' => [
                'listingParams' => [
                    'searchInfo' => [
                        'searchTermEntered' => $searchQuery,
                        'searchTermCorrected' => $searchQuery
                    ]
                ]
            ]
        ];
    }
}
