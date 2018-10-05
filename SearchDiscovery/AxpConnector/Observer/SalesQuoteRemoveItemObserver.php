<?php
namespace SearchDiscovery\AxpConnector\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteRemoveItemObserver implements ObserverInterface
{
    /**
     * @var \SearchDiscovery\AxpConnector\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    protected $logger;


    /**
     * @param \SearchDiscovery\AxpConnector\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository,
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     */
    public function __construct(\SearchDiscovery\AxpConnector\Helper\Data $helper,
                                \Magento\Catalog\Model\ProductRepository $productRepository,
                                \Psr\Log\LoggerInterface $logger,
                                \Magento\Checkout\Model\Session $_checkoutSession)
    {
        $this->helper = $helper;
        $this->_checkoutSession = $_checkoutSession;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $quoteItem = $observer->getData('quote_item');
        $productId = $quoteItem->getData('product_id');

        if (!$productId) {
            return $this;
        }

        $product = $this->productRepository->getById($productId);
        $qty = $quoteItem->getData('qty');

        $this->_checkoutSession->setRemoveFromCartDatalayerContent($this->helper->removeFromCartPushData($qty, $product));
        $this->logger->addInfo('Remove From Cart Observer');

        return $this;
    }
}