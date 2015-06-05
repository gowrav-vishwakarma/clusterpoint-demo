<?php

/**
 * Agiletoolkit with clusterpoint
 * Basic CRUD example for clusterpoint NoSQl Database
 * Date: 04.06.2015
 * Time: 14:25
 * Author: Romans <romans@agiletoolkit.org>
 */
class page_bank extends Page {

    public $title='Bank';

    function page_index(){

    	$g = $this->add('Grid');

        if($_GET['delete']){
            $this->api->cpsSimple->delete($_GET['delete']);
            $g->js()->univ()->reload()->execute();
        }

        if($_GET['edit']){
            $this->js()->univ()->dialogURL('EDIT',$this->api->url('./edit',array('id'=>$_GET['edit'])))->execute();
        }

    	$g->addColumn('name');
    	$g->addColumn('type');
    	$g->addColumn('balance');
    	$g->setSource($this->getAccounts());
    	$g->addButton('Open Account')->js('click')->univ()->dialogURL("Open Account",$this->api->url('./add'));
    	$g->addButton('Transfer')->js('click')->univ()->dialogURL("Transfer",$this->api->url('./transfer'));

        $g->addColumn('button','edit',array('icon'=>'trash','descr'=>'Edit'));
        $g->addColumn('confirm','delete',array('icon'=>'trash','descr'=>'Delete'));
    }

    function page_add(){
    	$f = $this->add('Form');
    	$f->addField('line','name');
    	$f->addField('line','balance');

    	$f->onSubmit(function($f){
    		$doc = $f->get();
    		$doc['type']="account";
    		$f->api->cpsSimple->insertSingle('acc_'.uniqid(),$doc);
            return $f->js()->univ()->location($f->api->url('..'));
        });
    }

    function page_edit(){
        $id = $this->api->stickyGET('id');
        $doc = $this->api->cpsSimple->retrieveMultiple($id, DOC_TYPE_ARRAY)[$id];
        $f = $this->add('Form');
        $f->addField('line','name')->set($doc[$id]['name'])->validateNotNull();

        $f->onSubmit(function($f)use($id, $doc){
            $doc['name'] = $f['name'];
            $ret = $this->api->cpsSimple->partialReplaceSingle($id,$doc);
    		return $f->js()->univ()->location($f->api->url('..'));
        });

    }

    function page_transfer(){
    	$acs =array_map(function($m){return $m['name'];}, $this->getAccounts());
    	$f = $this->add('Form');
    	$f->addField('line','amount');
    	$f->addField('DropDown','from')->setValueList($acs);
    	$f->addField('DropDown','to')->setValueList($acs);

    	$f->onSubmit(function($f){
    		$ids =array($f['from'],$f['to']) ;
    		$docs= $f->api->cpsSimple->retrieveMultiple($ids,DOC_TYPE_ARRAY);

    		if($docs[$f['from']]['balance'] < $f['amount'])
    			$f->error('from','Not Enough Money');

    		$docs[$f['from']]['balance'] -= $f['amount'];
    		$docs[$f['to']]['balance'] += $f['amount'];

    		$f->api->cpsSimple->updateMultiple($docs);

    		return $f->js()->univ()->location($f->api->url('..'));

    	});

    }

    function getAccounts(){

    	return ($this->app->cpsSimple->search('<type>account</type>',null,null,null,null,DOC_TYPE_ARRAY));

    	// return array(
    	// 		1=>array('type'=>'account','name'=>'Saving','balance'=>0),
    	// 		2=>array('type'=>'account','name'=>'Right Pocket','balance'=>500)
    	// 	);
    }

}
