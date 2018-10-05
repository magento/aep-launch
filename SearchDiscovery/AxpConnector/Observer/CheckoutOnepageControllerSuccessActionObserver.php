<?php
namespace SearchDiscovery\LaunchByAdobe\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutOnepageControllerSuccessActionObserver implements ObserverInterface
{
    const COOKIE_NAME = 'launchbyadobe_checkout_success';

    // Short duration, it just has to survive a page load
    const COOKIE_DURATION_SECS = 180;

    protected $helper;

    protected $logger;

    protected $cookieManager;

    protected $cookieMetadataFactory;


    public function __construct(\SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
                                \Psr\Log\LoggerInterface $logger,
                                \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
                                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory)
    {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $datalayerContent = $this->helper->orderPlacedPushData($orderIds);

        if(count($datalayerContent) > 0) {
            $jsonArray = $this->helper->jsonify($datalayerContent);
            $metadata = $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_DURATION_SECS);
            $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $jsonArray, $metadata);
        }
    }
}
