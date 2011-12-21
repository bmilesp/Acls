<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo $this->Html->css('/acls/css/tables');
echo $this->Html->script(array('/acls/js/jquery-1.4.2.min', '/acls/js/jquery-acl'));?>
<h2><?php echo $this->Html->link('ACOs', array('action' => 'index', 'plugin' => 'acls'))?></h2>
<div id="breadcrumbs">
<?php if (!empty($path)) {
    foreach($path as $id => $alias) {
        $this->Html->addCrumb($alias, array('action' => 'index', 'plugin' => 'acls', $id));
    }
}
echo $this->Html->getCrumbs(' &#8250; ');?>
</div>
<?php echo $this->Paginator->counter(array('format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'));
echo $this->Form->create('Aco', array('action' => 'delete', 'id' => 'aco-form'));?>
<table width="100%">
  <thead>
      <tr>
          <th width="25"><?php echo $this->Form->checkbox(null, array('id' => 'select-all'))?></th>
          <th width="25"><?php echo $this->Html->link($this->Html->image('/acls/img/add.png', array('alt' => 'Add ACO')), array('action' => 'add', 'plugin' => 'acls', $parent_id), array('escape' => false, 'title' => 'Add ACO'))?></th>
          <th width="75"></th>
          <th></th>
          <th>Alias</th>
          <th>Model</th>
          <th>ForeignKey</th>
      </tr>
  </thead>
<?php if (!empty($acos)) {?>
	<tbody>
    <?php foreach($acos as $i) {
        if (empty($count)) $count = 1; else $count++; ?>
        <tr class="<?php echo (($count % 2 == 0) ? 'even' : 'odd')?>">
          <td><?php echo $this->Form->checkbox('Aco.delete.' . $i['Aco']['id'])?></td>
          <td><?php echo $this->Html->link($this->Html->image('/acls/img/edit.png', array('alt' => 'Edit ACO')), array('action' => 'edit', 'plugin' => 'acls', $i['Aco']['id']), array('escape' => false, 'title' => 'Edit ACO'))?></td>
          <td><?php echo $this->Html->link($this->Html->image('/acls/img/permissions.png', array('alt' => 'View Permissions')), array('controller' => 'permissions', 'action' => 'index', 'plugin' => 'acls', $i['Aco']['id']), array('escape' => false, 'title' => 'View Permissions'))?> 
          		<small>(<?php echo $i['Aco']['num_permissions']?>)</small></td>
          <td><?php echo (($i['Aco']['num_children'] > 0) ? $this->Html->link('Children', array('action' => 'index', 'plugin' => 'acls', $i['Aco']['id'])) : 'Children')?> <small>(<?php echo $i['Aco']['num_children']?>)</small></td>
          <td><?php echo $i['Aco']['alias']?></td>
          <td><?php echo $i['Aco']['model']?></td>
          <td><?php echo $i['Aco']['foreign_key']?></td>
        </tr>
    <?php }?>
	</tbody>
<?php } ?>
</table>
<div class="paging">
<?php
$this->Paginator->options(array('url' => array('plugin' => 'acls')));
echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
echo $this->Paginator->numbers(array('separator' => ''));
echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div>
<?php
echo $this->Form->hidden('parent_id', array('value' => $parent_id));
echo $this->Form->submit('Delete Selected', array('after' => ' <input type="submit" value="Rebuild ACOs" id="rebuildButton" />'));
echo $this->Form->end();
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#rebuildButton').click(function() {
            $('#aco-form').attr('action', '/acls/acos/rebuild').submit();
        });
    });
</script>