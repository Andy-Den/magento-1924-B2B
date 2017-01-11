/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2013 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

function toggleWarehouses(buttonElement, warehousesElement)
{
    var buttonObject = $(buttonElement);
    var warehousesObject = $(warehousesElement);
    if (buttonObject.hasClassName('expanded')) {
        buttonObject.select('.expanded').each(function (expandedElement) { $(expandedElement).hide(); });
        buttonObject.select('.collapsed').each(function (collapsedElement) { $(collapsedElement).show(); });
        buttonObject.removeClassName('expanded');
        warehousesObject.hide();
    } else {
        buttonObject.select('.expanded').each(function (expandedElement) { $(expandedElement).show(); });
        buttonObject.select('.collapsed').each(function (collapsedElement) { $(collapsedElement).hide(); });
        buttonObject.addClassName('expanded');
        warehousesObject.show();
    }
}