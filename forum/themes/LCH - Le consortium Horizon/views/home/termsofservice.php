<?php if (!defined('APPLICATION')) exit(); ?>

<h1><?php echo t('TermsOfService'); ?></h1>
<div class="Legal">
    Pour intégrer la Guilde vous devez <a href="/forum/page/presentation-de-la-guilde" target="_blank">prendre connaissance et accepter sa Charte</a>.
    <?php echo Gdn_Format::Markdown(t('TermsOfServiceText')); ?>
</div>
