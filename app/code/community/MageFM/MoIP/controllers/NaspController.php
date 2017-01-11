<?php

class MageFM_MoIP_NaspController extends Mage_Core_Controller_Front_Action
{

    public function notifyAction()
    {
        if ($this->getRequest()->getParam('authkey') != 'kRkwYLeFvTEsAmgMzzsBS2GEjzuP32LQL8AZdpCP') {
            $this->getResponse()->setHttpResponseCode(401);
            $this->getResponse()->setBody('Unauthorized' . PHP_EOL);
            return;
        }

        $dados = $this->getRequest()->getParams();
        Mage::log(print_r($dados, true), null, 'moip-nasp.log', true);

        try {
            $id = $this->getRequest()->getParam('id_transacao');

            if (empty($id)) {
                throw new Exception('Order not found.');
            }

            $order = Mage::getModel('sales/order')->loadByIncrementId($id);

            if (empty($order) || !$order->getId()) {
                throw new Exception('Order not found.');
            }

            switch ($this->getRequest()->getParam('status_pagamento')) {
                case 1: // Autorizado
                    $this->invoice($order, $dados);
                    break;
                case 5: // Cancelado
                    $this->cancel($order, $dados);
                    break;
            }

            $this->getResponse()->setHttpResponseCode(200);
            $this->getResponse()->setBody('OK' . PHP_EOL);
        } catch (Exception $e) {
            Mage::log($e->getMessage() . '|' . print_r($dados, true), null, 'moip-nasp-error.log', true);
            $this->getResponse()->setHttpResponseCode(404);
            $this->getResponse()->setBody($e->getMessage() . PHP_EOL);
        }
    }

    protected function invoice(Mage_Sales_Model_Order $order, $dados = null)
    {
        $invoice = $order->prepareInvoice();

        Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

        $invoice->sendEmail();
        $invoice->setEmailSent(true);
        $invoice->save();

        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', 'Notificado pelo NASP do MoIP', true, true);
        $order->save();
    }

    protected function cancel(Mage_Sales_Model_Order $order, $dados = null)
    {
        $comment = "Cancelamento automático do MoIP";

        if (!empty($dados['classificacao'])) {
            $comment .= "<br />classificacao: {$dados['classificacao']}";
        }

        $order->cancel();
        $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, 'canceled', $comment, true, true);
        $order->save();
    }

}
