<?php

if(!$filter = $this->a('filter')) return;

$action_prefix = $filter['_action_prefix'] ? $filter['_action_prefix'] : '/admin';
$method = $filter['_method'] ? $filter['_method'] : 'get';

?><form class="form-inline" action="<?= $action_prefix . $filter['_action'] ?>" method="<?= $method ?>">
    <?php foreach($filter as $key => $value):
        if($key{0} === '_') continue;
    ?>
      <div class="form-group">
        <input type="text" class="form-control" name="<?= $key ?>" placeholder="<?= $value ?>" value="<?= $method === 'get' ? $this->get_query($key) : $this->get_post($key)?>">
      </div>
    <?php endforeach ?>

    <button type="submit" class="btn btn-cyan"><i class="fa fa-search" title="<?= $this->text('regular-search') ?>"></i></button>
</form>
