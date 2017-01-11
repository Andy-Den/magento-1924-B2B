<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 12/3/15
 * Time: 10:52 AM
 */

require_once './configScript.php';

printCategories(1);

function printCategories($parentId)
{
	$categories = Mage::getModel('catalog/category')->getCollection()
		->addFieldToFilter('parent_id', $parentId);

	foreach ($categories as $category)
	{
		printLabel($category);
		printCategories($category->getId());
	}
}

function printLabel($category)
{
	$category->load();

	$count = 1;
	$prefix = '';
	while ($count < $category->getLevel())
	{
		$prefix = ($prefix . '+');
		$count++;
	}
	if ($category->getLevel() == 1)
	{
		echo("[ " . $prefix . $category->getId() . " " . $category->getName() . " ]\n");
	} else
	{
		echo($prefix . $category->getId() . " " . $category->getName() . "\n");
	}
}