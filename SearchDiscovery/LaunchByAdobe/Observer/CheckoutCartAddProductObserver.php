<?php
namespace SearchDiscovery\LaunchByAdobe\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutCartAddProductObserver implements ObserverInterface
{
    /**
     * @var \SearchDiscovery\LaunchByAdobe\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \SearchDiscovery\LaunchByAdobe\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     */
    public function __construct(\SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
                                \Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Checkout\Model\Session $_checkoutSession)
    {
        $this->helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_checkoutSession = $_checkoutSession;
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

        $product = $observer->getData('product');
        $request = $observer->getData('request');

        $params = $request->getParams();

        if (isset($params['qty'])) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
            );
            $qty = $filter->filter($params['qty']);
        } else {
            $qty = 1;
        }

        $datalayerContent = $this->helper->addToCartPushData($qty, $product);
        $this->_checkoutSession->setAddToCartDatalayerContent($datalayerContent);

        return $this;
    }
}