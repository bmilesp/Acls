<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PermissionsController extends AppController {
    var $name = 'Permissions';
    var $uses = array('Permission', 'User', 'Group');
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->Permission->validate = array(
            'aro_id' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'ARO is required',
            )
        );
		
		$this->Auth->allow('*');
    }
    
    function beforeRender() {
        parent::beforeRender();
        $perms = array(
            '1' => 'Allow',
            '0' => 'Inherit',
           '-1' => 'Deny',
        );
        $this->set(compact('perms'));
    }
    
    function index($aco_id) {
        $this->Permission->Aco->actsAs[] = 'Tree';
        $path = $this->_getAcoPathList($aco_id);
        $permissions = $this->Permission->find('all', array('conditions' => array('aco_id' => $aco_id)));
        foreach($permissions as $key => $i) {
            $path2 = $this->_getAcoPathList($i['Permission']['aco_id']);
            $permissions[$key]['Permission']['path'] = implode('/', $path2);
        }
        $users = $this->User->find('list', array('fields' => array('id', 'username'), 'order' => 'username'));
        $groups = $this->Group->find('list', array('order' => 'name'));
        $this->set(compact('permissions', 'aco_id', 'path', 'users', 'groups'));
    }
    
    function add($aco_id = null) {
        if (!empty($this->request->data)) {
            if ($this->Permission->save($this->request->data)) {
                $this->Session->setFlash('Permission Added');
                $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
            }
        } else {
            $this->request->data['Permission']['aco_id'] = $aco_id;
            $this->request->data['Permission']['_create'] = 0;
            $this->request->data['Permission']['_read'] = 0;
            $this->request->data['Permission']['_update'] = 0;
            $this->request->data['Permission']['_delete'] = 0;
        }
        $path = $this->_getAcoPathList($this->request->data['Permission']['aco_id']);
        $aros = $this->_getAroList();
        $this->set(compact('aros', 'path'));
    }
    
    function edit($aco_id, $id = null) {
        if (!empty($this->request->data)) {
            if ($this->Permission->save($this->request->data)) {
                $this->Session->setFlash('Permission Updated');
                $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
            }
        } else {
            $permission = $this->Permission->findById($id);
            if (empty($permission)) {
                $this->Session->setFlash('Invalid Permission ID');
                $this->redirect(array('action' => 'add', $aco_id));
            } else {
                $this->request->data = $permission;
            }
        }
        $path = $this->_getAcoPathList($this->request->data['Permission']['aco_id']);
        $aros = $this->_getAroList();
        $this->set(compact('aros', 'path'));
    }
    
    function delete() {
        $delete_count = 0;
        if (!empty($this->request->data['Permission']['delete'])) {
            foreach($this->request->data['Permission']['delete'] as $id => $delete) {
                if ($delete == 1) {
                    if ($this->Permission->delete($id)) {
                        $delete_count++;
                    }
                }
            }
        }
        $this->Session->setFlash($delete_count . ' Permission' . (($delete_count == 1) ? ' was' : 's were') . ' deleted');
        $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
    }
    
    function _bindModels() {
        $this->Permission->Aro->bindModel(
            array(
                'belongsTo' => array(
                    'Group' => array(
                        'className' => 'Group',
                        'foreignKey' => 'foreign_key',
                        'conditions' => array('Aro.model' => 'Group'),
                    ),
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'foreign_key',
                        'conditions' => array('Aro.model' => 'User'),
                    ),
                )
            )
        );
    }
    
    function _getAroList() {
        $groups = $this->Group->find('list', array('fields' => array('id', 'name'), 'order' => 'Group.name'));
        foreach($groups as $group_id => $group_name) {
            $aros[$this->_getAroId('Group', $group_id)] = $group_name;
            $users = $this->User->find('list', array('fields' => array('id', 'username'), 'conditions' => array('group_id' => $group_id), 'order' => 'User.username'));
            foreach($users as $user_id => $username) {
                $aros[$this->_getAroId('User', $user_id)] = '-- ' . $username;
            }
        }
        return $aros;
    }
    
    function _getAroId($model, $foreign_key) {
        return $this->Permission->Aro->field('id', array('model' => $model, 'foreign_key' => $foreign_key));
    }
    
    function _getAcoPathList($aco_id) {
        $_path = $this->Permission->Aco->getPath($aco_id);
        foreach($_path as $i) {
            $path[$i['Aco']['id']] = $i['Aco']['alias'];
        }
        return $path;
    }
}
?>
