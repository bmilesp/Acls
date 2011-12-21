<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AcosController extends AppController {
    var $name = 'Acos';
    var $components = array('Acls.AcoBuilder');
    var $root_id;
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->root_id = $this->Aco->field('id', array('alias' => substr($this->Auth->actionPath, 0, -1)));
        $this->Aco->virtualFields['depth'] = 'SELECT COUNT(*) FROM acos WHERE acos.lft < Aco.lft AND acos.rght > Aco.rght';
        $this->Aco->validate = array(
            'alias' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Alias is required',
            )
        );
		
		$this->Auth->allow('*');
    }
    
    function validateAco($data) {
        if (empty($data['Model']['alias'])) {
            $errors['Acl']['alias'] = 'Alias is required';
        }
        if (empty($errors)) {
            return true;
        } else {
            $this->set(compact('errors'));
            return false;
        }
    }
    
    function index($parent_id = null) {
        if (empty($parent_id)) {
            $parent_id = $this->root_id;
        } else {
            $this->Aco->actsAs[] = 'Tree';
            $path = $this->_getAcoPathList($parent_id);
        }
        $actionpath = substr($this->Auth->actionPath, 0, -1);
        $this->Aco->virtualFields['num_permissions'] = 'SELECT COUNT(*) FROM aros_acos x WHERE x.aco_id = Aco.id';
        $this->Aco->virtualFields['num_children'] = 'CAST((Aco.rght - Aco.lft -1) / 2 AS UNSIGNED)';
        $conditions = array();
        if(!empty($parent_id)){
        	 $conditions = array('parent_id' => $parent_id);
		}
		$this->paginate = array(
			'order' => 'lft',
			'conditions' => $conditions,
			'limit' => 20
		);
        $acos = $this->paginate('Aco');
        $this->set(compact('acos', 'path', 'actionpath', 'parent_id'));
    }
    
    function add($parent_id = null) {
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Aco']['parent_id'])) {
                $this->request->data['Aco']['parent_id'] = $this->root_id;
            }
            if ($this->Aco->save($this->request->data)) {
                $this->Session->setFlash('ACO Created');
                $this->redirect(array('action' => 'index', $this->request->data['Aco']['parent_id']));
            }
        } else {
            $this->request->data['Aco']['parent_id'] = $parent_id;
        }
        $parents = $this->_getParenstsList();
        $this->set(compact('parents'));
    }
    
    function edit($id = null) {
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Aco']['parent_id'])) {
                $this->request->data['Aco']['parent_id'] = $this->root_id;
            }
            if ($this->Aco->save($this->request->data)) {
                $this->Session->setFlash('ACO Updated');
                $this->redirect(array('action' => 'index', $this->request->data['Aco']['parent_id']));
            }
        } else {
            $aco = $this->Aco->findById($id);
            if (empty($aco)) {
                $this->Session->setFlash('Invalid ACO ID');
                $this->redirect('add');
            } else {
                $this->request->data = $aco;
            }
        }
        $parents = $this->_getParenstsList();
        $this->set(compact('parents'));
    }
    
    function delete() {
        $delete_count = 0;
        if (!empty($this->request->data['Aco']['delete'])) {
            foreach($this->request->data['Aco']['delete'] as $id => $delete) {
                if ($delete == 1) {
                    if ($this->Aco->delete($id)) {
                        $delete_count++;
                    }
                }
            }
        }
        $this->Session->setFlash($delete_count . ' ACO' . (($delete_count == 1) ? ' was' : 's were') . ' deleted');
        $this->redirect(array('action' => 'index', $this->request->data['Aco']['parent_id']));
    }
    
    function rebuild() {
        if (!empty($this->request->data)) {
            $this->Session->setFlash('ACOs were rebuilt');
            $this->AcoBuilder->build_acl();
            $this->redirect('index');
        }
    }
    
    function _getParenstsList() {
        $acos = $this->Aco->find('all', array('order' => 'lft', 'conditions' => array('lft >' => 1)));
        foreach($acos as $key => $i) {
            $parents[$i['Aco']['id']] = str_repeat('-- ', $i['Aco']['depth']) . $i['Aco']['alias'];
        }
        return $parents;
    }
    
    function _getAcoPathList($aco_id) {
        $_path = $this->Aco->getPath($aco_id);
        foreach($_path as $i) {
            $path[$i['Aco']['id']] = $i['Aco']['alias'];
        }
        return $path;
    }
}
?>
