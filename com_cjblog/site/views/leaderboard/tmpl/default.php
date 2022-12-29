<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

$layout = $this->params->get( 'ui_layout', 'default' );
?>
<div id="cj-wrapper" class="leaderboard<?php echo $this->pageclass_sfx; ?>">
	<?php echo JLayoutHelper::render( $layout . '.toolbar', [ 'params' => $this->params, 'state' => $this->state ] ); ?>
	<?php echo JLayoutHelper::render( $layout . '.leaderboard', [ 'items' => $this->items, 'params' => $this->params ] ); ?>
	<?php echo JLayoutHelper::render( $layout . '.credits', [ 'params' => $this->params ] ); ?>
</div>
