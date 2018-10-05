<?php
namespace SearchDiscovery\AxpConnector\Block;

class Page extends \Magento\Framework\View\Element\Template
{

  protected $helper;
  protected $pageTitle;
  protected $catalogHelper;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \SearchDiscovery\AxpConnector\Helper\Data $helper,
    \Magento\Catalog\Helper\Data $catalogHelper,
    \Magento\Framework\View\Page\Title $pageTitle,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->helper = $helper;
    $this->pageTitle = $pageTitle;
    $this->catalogHelper = $catalogHelper;
  }

  public function datalayerPage() {
    $title = $this->pageTitle();
    $type = $this->pageType();
    $crumbs = $this->getBreadCrumbPath();

    return $this->helper->pageLoadedPushData($title, $type, $crumbs);
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

  protected function pageTitle() {
    return $this->pageTitle->getShort();
  }

  protected function pageType() {
    return $this->_request->getFullActionName();
  }


}