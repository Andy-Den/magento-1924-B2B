<?php
/**
 * MageParts
 * 
 * NOTICE OF LICENSE
 * 
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright 
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates. 
 * For information regarding modifications see http://www.magentocommerce.com.
 *  
 * DISCLAIMER
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE 
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_ConfirmCustomer
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author 	   MageParts Crew
 */

// we need to do this for the extension to show up in the `core_resources`, and actually being counted as installed, on every version of Magento.

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// get customer entity table
$customerTable = $installer->getTable('customer_entity');

$connection = $this->getConnection();

// add "approved" culoumn to customer table
$connection->addColumn($customerTable, 'mp_cc_is_approved', "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");

// update all existing customer records to be "approved"
$installer->run("
	UPDATE `{$this->getTable('customer_entity')}` SET `mp_cc_is_approved` = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."';
");

$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Account Awaiting Approval',
    'root_template'     => 'one_column',
    'identifier'        => 'account-awaiting-approval',
    'content'           => "<div class=\"page-title\">\r\n        <h1><a name=\"top\"></a>Your account is awaiting approval</h1>\r\n    </div>\r\n    <p>Your account has been created but needs to be approved by an administrator before you can sign in.</p>\r\n<p>An e-mail will be sent to the e-mail address you used when you registered this account once it has been approved.</p>\r\n<p>Thank you for your patience.</p>",
    'creation_time'     => now(),
    'update_time'       => now(),
));

$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));

$installer->endSetup();