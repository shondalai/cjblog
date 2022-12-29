<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

$categoryId = $displayData['catid'];
$params     = $displayData['params'];
$state      = $displayData['state'];
$theme      = $params->get( 'theme', 'default' );
$layout     = $params->get( 'ui_layout', 'default' );
$categories = JCategories::getInstance( 'Content', [ 'countItems' => true ] );
$category   = $categories->get( $categoryId );

if ( $params->get( 'list_show_search_form', 1 ) == 1 || $params->get( 'show_category_list', 1 ) == 1 )
{
	?>
    <div class="card card-<?php echo $theme; ?>">
		<?php
		if ( $params->get( 'list_show_category_list', 1 ) == 1 )
		{
			?>
            <div class="card-header">
                <span><i class="fa fa-folder-open"></i> <?php echo JText::_( 'COM_CJBLOG_CATEGORIES_LABEL' ); ?></span>
				<?php if ( ! empty( $category->parent_id ) ): ?>
                    <a href="<?php echo JRoute::_( CjBlogHelperRoute::getCategoryRoute( $category ) ); ?>">
                        <small>: <?php echo $this->escape( $category->title ) . ( $category->numitems ? ' (' . $category->numitems . ')' : '' ); ?></small>
                    </a>
				<?php endif; ?>
            </div>
			<?php
		}
		?>
        <div class="card-body">
			<?php
			if
			(
				$params->get( 'list_show_category_list', 1 ) == 1 && ! empty( $category->id )
				&& ( ( ! empty( $category->description ) && $params->get( 'list_show_description' ) ) || count( $category->getChildren() ) > 0 )
			)
			{
				?>
                <div class="mb-3">
					<?php echo JLayoutHelper::render( $layout . '.category_list', [ 'category' => $category, 'params' => $params, 'maxlevel' => 1 ] ); ?>
                </div>
				<?php
			}

			if ( $params->get( 'list_show_search_form', 1 ) == 1 )
			{
				echo JLayoutHelper::render( $layout . '.search_form', [ 'params' => $params, 'state' => $state, 'catid' => ( isset( $category->id ) ? $category->id : 0 ) ] );
			}
			?>
        </div>
    </div>
	<?php
}