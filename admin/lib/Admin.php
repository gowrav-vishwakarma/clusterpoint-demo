<?php

class Admin extends App_Admin {

    function init() {
        parent::init();

        $connectionStrings = array(
          'tcp://cloud-us-0.clusterpoint.com:9007',
          'tcp://cloud-us-1.clusterpoint.com:9007',
          'tcp://cloud-us-2.clusterpoint.com:9007',
          'tcp://cloud-us-3.clusterpoint.com:9007',
        );
        
        $this->cpsConn = new CPS_Connection(new CPS_LoadBalancer($connectionStrings), 
            'bank',
            'demoapps@clusterpoint.com',
            $this->getConfig('cps/password'),
            'document',
            '//document/id',
            array('account' => 100028)
        );
        //$cpsConn->setDebug(true);
        $this->cpsSimple = new CPS_Simple($this->cpsConn); 

        $this->api->pathfinder
            ->addLocation(array(
                'addons' => array('addons', 'vendor'),
            ))
            ->setBasePath($this->pathfinder->base_location->getPath() . '/..')
        ;

        $this->api->menu->addMenuItem('/', 'Home');
        $this->api->menu->addMenuItem('/bank', array('Bank','icon'=>'building'));
        $this->api->menu->addMenuItem('/users', array('Users','icon'=>'users'));
    }
}



        // For improved compatibility with Older Toolkit. See Documentation.
        // $this->add('Controller_Compat42')
        //     ->useOldTemplateTags()
        //     ->useOldStyle()
        //     ->useSMLite();
