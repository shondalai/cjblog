<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

$user     = JFactory::getUser();
$access   = $user->getAuthorisedViewLevels();
$params   = $displayData['params'];
$theme    = $params->get( 'theme', 'default' );
$item     = $displayData['category'];
$maxlevel = $displayData['maxlevel'];
$columns  = (int) $params->get( 'max_category_columns', 3 );

if ( ! in_array( $item->id, $params->get( 'exclude_categories', [] ) ) )
{
	$catParams = new JRegistry();
	if ( ! empty( $item->params ) )
	{
		$catParams->loadString( $item->params );
	}
	?>
    <div class="d-flex mb-3">
		<?php
		$categoryUrl = CjBlogHelperRoute::getCategoryRoute( $item );
		if ( $params->get( 'show_description_image', 1 ) && ! empty( $catParams->get( 'image' ) ) )
		{
			?>
            <div class="flex-shrink-0 d-none d-md-block me-3">
                <a href="#" class="thumbnail" onclick="return false;">
                    <img src="<?php echo $catParams->get( 'image' ); ?>" alt="<?php echo $this->escape( $catParams->get( 'image_alt' ) ); ?>" class="media-object"
                         style="max-width: 128px;">
                </a>
            </div>
			<?php
		}
		?>
        <div class="flex-grow-1">
			<?php
			if ( $params->get( 'show_parent' ) && $item->parent_id )
			{
				?>
                <h5>
                    <a href="<?php echo JRoute::_( $categoryUrl ); ?>"><?php echo $this->escape( $item->title ); ?></a>

					<?php if ( $params->get( 'show_feed_link', 1 ) == 1 ): ?>
                        <a href="<?php echo JRoute::_( $categoryUrl . '&format=feed&type=rss' ); ?>"
                           title="<?php echo JText::_( 'COM_CJBLOG_RSS_FEED' ); ?>" data-toggle="tooltip">
                            <sup class="margin-left-5"><small><i class="fa fa-rss-square"></i></small></sup>
                        </a>
					<?php endif; ?>
                </h5>
				<?php
			}

			if ( $params->get( 'show_parent' ) && $item->parent_id )
			{
				?>
                <hr class="no-margin-top margin-bottom-5">
				<?php
			}

			if ( ! empty( $item->description ) && $params->get( 'show_description' ) )
			{
				echo JHtml::_( 'content.prepare', $item->description, '', 'com_cjblog.categories' );
			}
			?>
        </div>
    </div>
	<?php
	if ( $maxlevel != 0 && count( $item->getChildren() ) > 0 )
	{
		$categories = $item->getChildren();
		?>
        <div class="row">
			<?php
			foreach ( $categories as $node )
			{
				if ( in_array( $node->access, $access ) && ! in_array( $node->id, $params->get( 'exclude_categories', [] ) ) )
				{
					?>
                    <div class="col">
                        <a href="<?php echo JRoute::_( CjBlogHelperRoute::getCategoryRoute( $node ) ); ?>">
							<?php echo $this->escape( $node->title ); ?>
                            <span class="text-muted d-block d-md-none">(<?php echo JText::plural( 'COM_CJBLOG_NUM_ARTICLES', $node->numitems ); ?>)</span>
                        </a>

						<?php if ( $params->get( 'show_feed_link', 1 ) == 1 ): ?>
                            <a href="<?php echo JRoute::_( CjBlogHelperRoute::getCategoryRoute( $node ) . '&format=feed&type=rss' ); ?>"
                               title="<?php echo JText::_( 'COM_CJBLOG_RSS_FEED' ); ?>" data-toggle="tooltip">
                                <sup class="ms-2"><small><i class="fa fa-rss-square"></i></small></sup>
                            </a>
						<?php endif; ?>

                        <small class="text-muted d-none d-md-inline-block">(<?php echo $node->numitems; ?>)</small>
                    </div>
					<?php
				}
			}
			?>
        </div>
		<?php
	}
}