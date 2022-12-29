<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

JHtml::addIncludePath( JPATH_COMPONENT . '/helpers' );

$params     = $this->params;
$layout     = $params->get( 'ui_layout', 'default' );
$categoryId = $this->state->get( 'filter.category_id', 'root' );
?>
<div id="cj-wrapper" class="articles-list<?php echo $this->pageclass_sfx; ?>">
	<?php
	echo JLayoutHelper::render( $layout . '.toolbar', [ 'params' => $params, 'state' => $this->state ] );
	echo JLayoutHelper::render( $layout . '.category_search_box', [ 'catid' => $categoryId, 'params' => $params, 'state' => $this->state ], '', [ 'debug' => false ] );
	echo JLayoutHelper::render( $layout . '.articles_list', [ 'data' => $this ], '', [ 'debug' => false ] );
	echo JLayoutHelper::render( $layout . '.credits', [ 'params' => $params ] ); ?>
</div>
