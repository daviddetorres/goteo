<?php

$uploads_name = $form['uploads']->vars['full_name'];
$current_name = $form['current']->vars['full_name'];
$current_files = $value;

// print_r($current_files);die;
?>
<div class="dropfiles" data-text-upload="<?= $view->escape($text_upload) ?>" data-text-max-files-reached="<?= $view->escape($text_max_files_reached) ?>" data-text-file-type-error="<?= $view->escape($text_file_type_error) ?>" data-limit="<?= $limit ?>" data-multiple="<?= $attr['multiple'] ? 1 : 0 ?>" data-url="<?= $view->escape($url) ?>" data-current="<?= $view->escape($current_name) ?>" data-name="<?= $view->escape($uploads_name) ?>" data-markdown-link="<?= $view->escape($markdown_link) ?>" data-accepted-files="<?= $view->escape($accepted_files) ?>">
    <div class="image-zone" data-section="<?= $key ?>">
        <ul class="list-inline image-list-sortable" id="list-sortable-<?= $key ?>">
          <?php foreach($current_files as $img) {
            if($img) {
                echo $view->render('bootstrap/dropfiles_item.html.php', [
                    'file_url' => $img->getLink(300, 300, true),
                    'file_name' => $img->getName(),
                    'file_type' => $img->getType(),
                    'markdown_link' => $markdown_link,
                    'text_send_to_markdown' => $text_send_to_markdown,
                    'text_delete_image' => $text_delete_image,
                    'hidden_input' => '<input type="hidden" name="' . $current_name . '" value="' . $view->escape($img->getName()) . '">'
                ]);
            }
          } ?>
        </ul>
        <div class="dragndrop"><div class="dropzone"></div></div>
    </div>
    <p class="text-danger error-msg hidden"></p>
</div>
