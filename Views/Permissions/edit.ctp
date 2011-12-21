<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo '<h2>Edit Permission to ' . implode('/', $path) . '</h2>';
echo $this->Form->create('Permission');
echo $this->Form->hidden('id');
echo $this->Form->hidden('aco_id');
echo $this->Form->input('aro_id', array('label' => 'Access Request Object'));
echo $this->Form->input('_create', array('options' => $perms));
echo $this->Form->input('_read', array('options' => $perms));
echo $this->Form->input('_update', array('options' => $perms));
echo $this->Form->input('_delete', array('options' => $perms));
echo $this->Form->submit('Submit', array('after' => ' or ' . $this->Html->link('Cancel', array('action' => 'index', $this->request->data['Permission']['aco_id']))));
echo $this->Form->end();
?>
