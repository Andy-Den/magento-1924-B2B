<?php
class FVets_Core_Model_Observer {

    public function addDeviceUserAgent($observer) {
        $action = $observer->getEvent()->getControllerAction();

        if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
            $action->getLayout()->getUpdate()
                ->addHandle('user_agent_ipad');
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
            $action->getLayout()->getUpdate()
                ->addHandle('user_agent_iphone');
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
            $action->getLayout()->getUpdate()
                ->addHandle('user_agent_blackberry');
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
            $action->getLayout()->getUpdate()
                ->addHandle('user_agent_android');
        }

        if( stristr($_SERVER['HTTP_USER_AGENT'],'mobile') ) {
            $action->getLayout()->getUpdate()
                ->addHandle('user_agent_mobile');
        }

    }

}