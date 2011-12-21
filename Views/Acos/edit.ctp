<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo '<h2>Edit ACO</h2>';
echo $this->Form->create('Aco');
echo $this->Form->hidden('id');
echo $this->Form->input('parent_id', array('empty' => 'None'));
echo $this->Form->input('alias');
echo $this->Form->input('model');
echo $this->Form->input('foreign_key');
echo $this->Form->submit('Submit', array('after' => ' or ' . $this->Html->link('Cancel', array('action' => 'index', $this->request->data['Aco']['parent_id']))));
echo $this->Form->end();
?>
