<?php

/**
 * Created by Konstantin Kolodnitsky
 * Date: 25.11.13
 * Time: 14:57
 */
class page_users extends Page {

    public $title='Users';

    function page_index(){
    	$g = $this->add('Grid');
    	$g->addColumn('name');
    	$g->addColumn('type');
    	$g->addColumn('balance');
    	$g->setSource($this->getUsers());
    	$g->addButton('Open Account')->js('click')->univ()->dialogURL("Open Account",$this->api->url('./add'));
    	$g->addButton('Transfer')->js('click')->univ()->dialogURL("TRansfer",$this->api->url('./transfer'));
    }

    function page_add(){
    	$f = $this->add('Form');
    	$f->addField('line','name');
    	$f->addField('line','balance');

    	$f->onSubmit(function($f){
    		$doc = $f->get();
    		$doc['type']="account";
    		$f->api->cpsSimple->insertSingle(uniqid(),$doc);
    		return $f->js()->univ()->location($f->api->url('..'));
    	});
    }


    function getUsers(){

    	return ($this->app->cpsSimple->search('<type>users</type>',null,null,null,null,DOC_TYPE_ARRAY));

    	// return array(
    	// 		1=>array('type'=>'account','name'=>'Saving','balance'=>0),
    	// 		2=>array('type'=>'account','name'=>'Right Pocket','balance'=>500)
    	// 	);
    }

}
