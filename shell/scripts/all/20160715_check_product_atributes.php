<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/15/16
 * Time: 4:06 PM
 */

$productsIdsErp = array(
    1975,
    2132,
    2154,
    2159,
    2160,
    2529,
    2574,
    2578,
    2579,
    2580,
    2582,
    2583,
    2598,
    2130,
    2135,
    1992,
    2589,
    2596,
    2158,
    1885,
    1899,
    1900,
    1919,
    2035,
    2248,
    2274,
    2620,
    2621,
    2623,
    2624,
    1904,
    2128,
    2143,
    2036,
    2320,
    2332,
    1902,
    1931,
    1960,
    2000,
    2077,
    2078,
    2537,
    2538,
    2539,
    2541,
    2550,
    2551,
    2552,
    2556,
    2557,
    2560,
    2561,
    2563,
    2564,
    2565,
    2566,
    2567,
    2568,
    2569,
    2441,
    2225,
    1870,
    2081,
    2012,
    2554,
    2555,
    2558,
    2570,
    2572,
    2068,
    2079,
    2111,
    2161,
    2166,
    2242,
    2323,
    2641,
    2642,
    2643,
    2644,
    2645,
    2646,
    2647,
    2648,
    2649,
    2650,
    2651,
    2652,
    2653,
    2654,
    2659,
    2660,
    2661,
    2662,
    2663,
    2065,
    2121,
    2122,
    2123,
    2089,
    2011,
    2074,
    2085,
    2172,
    1944,
    1941,
    1942,
    1947,
    2066,
    2067,
    2063
);

$websiteId = 12;
$storeId = 22;

foreach ($productsIdsErp as $productIdErp) {
    $select = Mage::getModel('catalog/product')->getCollection()
        ->addWebsiteFilter($websiteId)
        ->setStoreId($storeId)
        ->addAttributeToSelect('weight')
        ->addAttributeToFilter('id_erp', $productIdErp);

    $product = $select
        ->getFirstItem();

    if ($product->getId()) {
        echo $product->getSku() . "|" . $product->getWeight() . "\n";
    }
}