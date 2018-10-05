<?php
namespace SearchDiscovery\LaunchByAdobe\Block;

class Page extends \Magento\Framework\View\Element\Template
{

  protected $helper;
  protected $_logger;
  protected $pageTitle;
  protected $catalogHelper;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\LaunchByAdobe\Helper\Data $helper,
    \Magento\Catalog\Helper\Data $catalogHelper,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Framework\View\Page\Title $pageTitle,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->helper = $helper;
    $this->_logger = $logger;
    $this->pageTitle = $pageTitle;
    $this->catalogHelper = $catalogHelper;
  }

  public function pageTitle() {
    return $this->pageTitle->getShort();
  }

  public function pageType() {
    return $this->_request->getFullActionName();
  }

  public function breadcrumbs() {
    return $this->getBreadCrumbPath();
  }

  public function datalayerPage() {
    $datalayerPage = array(
      'event' => 'Page Loaded',
      'page' => array(
        'pageType' => $this->pageType(),
        'pageName' => $this->pageTitle(),
        'breadcrumbs' => $this->breadcrumbs()
      )
    );

    return $datalayerPage;
  }

  public function datalayerPageJson() {
    return $this->helper->jsonify($this->datalayerPage());
  }

  public function log($msg) {
    $this->_logger->addInfo($msg);
  }

  protected function getBreadCrumbPath()
  {
    $titleArray = [];
    $breadCrumbs = $this->catalogHelper->getBreadcrumbPath();

    foreach ($breadCrumbs as $breadCrumb) {
        $titleArray[] = $breadCrumb['label'];
    }

    return $titleArray;
  }
}