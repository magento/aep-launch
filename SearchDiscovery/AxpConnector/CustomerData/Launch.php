<?php
namespace SearchDiscovery\LaunchByAdobe\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Launch private data section
 */
class Launch extends \Magento\Framework\DataObject implements SectionSourceInterface
{

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $logger;

    /**
     * Constructor
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->jsonHelper = $jsonHelper;
        $this->_checkoutSession = $_checkoutSession;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    public function getSectionData()
    {
        $data = [];

        if($this->_checkoutSession->getAddToCartDatalayerContent()) {
            $data[] = $this->_checkoutSession->getAddToCartDatalayerContent();
        }
        $this->_checkoutSession->setAddToCartDatalayerContent(null);

        if($this->_checkoutSession->getRemoveFromCartDatalayerContent()) {
            $data[] = $this->_checkoutSession->getRemoveFromCartDatalayerContent();
        }
        $this->_checkoutSession->setRemoveFromCartDatalayerContent(null);

        // Get rid of nulls and empties
        $data = array_filter($data);

        $script = "";
        if(count($data) > 0) {
            $jsonData = $this->jsonHelper->jsonEncode($data);

            // So awful...
            $script = "<script type=\"text/javascript\">\nwindow.AppEventData =  window.AppEventData || [];\n";
            foreach($data as $event) {
                $jsonData = $this->jsonHelper->jsonEncode($event);
                $this->logger->addInfo($jsonData);
                $script = $script . "window.AppEventData.push({$jsonData});\n";
            }
            $script = $script . "</script>";
        }

        return [
            'datalayerScript' => $script
        ];
    }
}