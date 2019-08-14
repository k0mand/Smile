<?php
/**
 * Class RequestPriceButton
 *
 * @category  Smile
 * @package   Smile\Catalog
 * @author    Roman Koshyk <romadaaaa@gmail.com>
 */
namespace Smile\Catalog\Block\Catalog;

use Magento\Framework\View\Element\Template;

/**
 * Class RequestPriceButton
 * @package Smile\Catalog\Block\Catalog
 */
class RequestPriceButton extends Template
{
    /**
     * Get button label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return __('Request price');
    }
}
