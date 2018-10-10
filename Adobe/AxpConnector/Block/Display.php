<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\AxpConnector\Block;

/**
 * Display block.
 */
class Display extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $datalayerEvents;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adobe\AxpConnector\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->_logger = $logger;
        $this->datalayerEvents = [];
    }

    /**
     * Is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * Push event to datalayer.
     *
     * @param $eventData
     */
    public function pushDatalayerEvent($eventData)
    {
        array_push($this->datalayerEvents, $eventData);
    }

    /**
     * Encode events.
     *
     * @return array
     */
    public function encodedDatalayerEvents()
    {
        $encoded = array_map([$this->helper, 'jsonify'], $this->datalayerEvents);
        return $encoded;
    }
}
