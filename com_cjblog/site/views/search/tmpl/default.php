<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2021 BulaSikku Technologies Private Limited.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

$layout = $this->params->get( 'ui_layout', 'default' );
?>
<div id="cj-wrapper" class="search<?php echo $this->pageclass_sfx; ?>">
	<?php echo JLayoutHelper::render( $layout . '.toolbar', [ 'params' => $this->params, 'state' => null ] ); ?>

	<?php if ( $this->params->get( 'show_page_heading', 1 ) ) : ?>
        <h1 class="page-header no-space-top"> <?php echo $this->escape( $this->heading ); ?> </h1>
	<?php endif; ?>

	<?php echo JLayoutHelper::render( $layout . '.search', [ 'params' => $this->params ] ); ?>
</div>