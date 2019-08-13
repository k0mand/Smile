<?php
/**
 * Smile customer search results interface
 *
 * @category  Smile
 * @package   Smile\Customer
 * @author    Roman Koshyk <romadaaaa@gmail.com>
 */
namespace Smile\Customer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RequestSearchResultsInterface
 *
 * @package Smile\Customer\Api\Data
 */
interface RequestSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     *
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
