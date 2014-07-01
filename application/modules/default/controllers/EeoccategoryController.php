<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_EeoccategoryController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		
    }

    public function indexAction()
    {
		$eeoccategorymodel = new Default_Model_Eeoccategory();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();		$searchQuery = '';		$searchArray = array();		$tablecontent='';
		
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
		
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	
			$searchArray = array();
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
				$perPage = $this->_getParam('per_page',PERPAGE);
			
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
		}
		$dataTmp = $eeoccategorymodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall);		
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'eeoccategory';
		$eeoccategoryform = new Default_Form_eeoccategory();
		$eeoccategoryform->removeElement("submit");
		$elements = $eeoccategoryform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		$eeoccategorymodel = new Default_Model_Eeoccategory();	
		/*$data = $eeoccategorymodel->getsingleEeoccategoryData($id);
		$eeoccategoryform->populate($data);
		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->form = $eeoccategoryform;*/
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$data = $eeoccategorymodel->getsingleEeoccategoryData($id);
				if(!empty($data) && $data != "norows")
				{
					$eeoccategoryform->populate($data[0]);
					$this->view->form = $eeoccategoryform;
					$this->view->controllername = $objName;
					$this->view->id = $id;
				    $this->view->ermsg = '';
				}
				else
				{
					 $this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->ermsg = 'nodata';
				$this->view->form = $eeoccategoryform;
			}
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
	}
	
	
	public function editAction()
	{	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$objName = 'eeoccategory';
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')	
			$this->_helper->layout->disableLayout();
		
		$eeoccategoryform = new Default_Form_eeoccategory();
		$eeoccategorymodel = new Default_Model_Eeoccategory();
		try
		{
			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$data = $eeoccategorymodel->getsingleEeoccategoryData($id);
					if(!empty($data) && $data != "norows")
					{
						$eeoccategoryform->populate($data[0]);
						$eeoccategoryform->submit->setLabel('Update'); 
						$this->view->form = $eeoccategoryform;
						$this->view->controllername = $objName;
						$this->view->id = $id;
						$this->view->ermsg = '';
					}
					else
					{
						 $this->view->ermsg = 'norecord';
					}
				}
				else
				{
					$this->view->ermsg = 'nodata';
				}
			}
			else
			{	//Add Record...
				$this->view->ermsg = '';
				$this->view->form = $eeoccategoryform;
			}
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
		    if($eeoccategoryform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $eeoccategory = $this->_request->getParam('eeoccategory');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$menumodel = new Default_Model_Menu();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'eeoccategory'=>trim($eeoccategory),
				   				  'description'=>trim($description),
								  'modifiedby'=>$loginUserId,
								//  'modifieddate'=>$date->get('yyyy-MM-dd HH:mm:ss')
									'modifieddate'=>gmdate("Y-m-d H:i:s")	
						);
					if($id!=''){
						$where = array('id=?'=>$id);  
						$actionflag = 2;
					}
					else
					{
					    $data['createdby'] = $loginUserId;
						//$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					//echo "<pre>";print_r($data);exit;
					$Id = $eeoccategorymodel->SaveorUpdateEeocCategoryData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					  // $this->_helper->getHelper("FlashMessenger")->addMessage("Eeoc Category updated successfully.");
					    $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"EEOC category updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
                        //$this->_helper->getHelper("FlashMessenger")->addMessage("Eeoc Category added successfully.");					   
						 $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"EEOC category added successfully."));
					}   
					$menuidArr = $menumodel->getMenuObjID('/eeoccategory');
					$menuID = $menuidArr[0]['id'];
					//echo "<pre>";print_r($menuidArr);exit;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
					//echo $result;exit;
    			    $this->_redirect('eeoccategory');		
			}else
			{
     			$messages = $eeoccategoryform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							//echo $key." >> ".$val2;
							$msgarray[$key] = $val2;
                                                        break;
						 }
					}
				$this->view->msgarray = $msgarray;
			
			}
		}
	}
	
	public function deleteAction()
	{
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $loginUserId = $auth->getStorage()->read()->id;
            }
            $id = $this->_request->getParam('objid');
            $messages['message'] = ''; $messages['msgtype'] = '';
            $messages['flagtype'] = '';
            $actionflag = 3;
            if($id)
            {
                $eeoccategorymodel = new Default_Model_Eeoccategory();
                $menumodel = new Default_Model_Menu();
                $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
                $where = array('id=?'=>$id);
                $ee_data = $eeoccategorymodel->getsingleEeoccategoryData($id);
                //print_r($ee_data);exit;
                $Id = $eeoccategorymodel->SaveorUpdateEeocCategoryData($data, $where);
                if($Id == 'update')
                {
                    sapp_Global::send_configuration_mail("EEOC Categories", $ee_data[0]['eeoccategory']);
                    $menuidArr = $menumodel->getMenuObjID('/eeoccategory');
                    $menuID = $menuidArr[0]['id'];
                         //echo "<pre>";print_r($objid);exit;
                    $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
                    $messages['message'] = 'EEOC category deleted successfully.';
                    $messages['msgtype'] = 'success';
				  // $messages['flagtype'] = 'process';
                }   
                else
                {
                    $messages['message'] = 'EEOC category cannot be deleted.';
                    $messages['msgtype'] = 'error';	
					//$messages['flagtype'] = 'process';	
                }
            }
            else
            { 
                $messages['message'] = 'EEOC category cannot be deleted.';$messages['msgtype'] = 'error';	
					//$messages['flagtype'] = 'process';	
            }
            $this->_helper->json($messages);		
	}//end of delete		
}
