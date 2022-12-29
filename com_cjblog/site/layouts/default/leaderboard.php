<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die();

$items            = $displayData['items'];
$params           = $displayData['params'];
$avatar           = $params->get( 'avatar_component', 'cjblog' );
$profileComponent = $params->get( 'profile_component', 'cjblog' );
$displayName      = $params->get( 'display_name', 'name' );
$api              = new CjLibApi();

if ( ! empty( $items ) )
{
	?>
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo JText::_( 'COM_CJBLOG_LEADERBOARD' ); ?></div>
        <ul class="list-group no-space-left">
			<?php
			foreach ( $items as $rank => $item )
			{
				$author     = $this->escape( $item->$displayName );
				$profileUrl = $api->getUserProfileUrl( $profileComponent, $item->id );
				$userAvatar = $api->getUserAvatarImage( $avatar, $item->id, $item->email, 64, true );
				?>
                <li class="list-group-item no-margin-left">
                    <div class="media">
                        <div class="media-left">
                            <div class="panel panel-success leader-rank-box" style="min-width: 75px; min-height: 72px;">
                                <h2 class="leader-rank center text-center"><?php echo $rank + 1; ?></h2>
                                <div class="muted text-muted center text-center"><?php echo JText::_( 'COM_CJBLOG_RANK_LABEL' ); ?></div>
                            </div>
                        </div>

						<?php if ( $avatar != 'none' ): ?>
                            <div class="media-left hidden-phone hidden-xs">
								<?php if ( $profileComponent != 'none' ): ?>
                                    <a href="<?php echo $profileUrl; ?>" title="<?php echo $author ?>" class="thumbnail no-margin-bottom" data-toggle="tooltip">
                                        <img src="<?php echo $userAvatar; ?>" alt="<?php echo $author; ?>" style="max-width: 64px;">
                                    </a>
								<?php else: ?>
                                    <div class="thumbnail no-margin-bottom">
                                        <img src="<?php echo $userAvatar; ?>" alt="<?php echo $author; ?>" style="max-width: 64px;">
                                    </div>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
                        <div class="media-body">
                            <h4 class="margin-top-5 margin-bottom-5"><?php echo $author; ?></h4>
                            <div class="muted text-muted">
                                <ul class="unstyled inline list-unstyled list-inline">
                                    <li class="no-pad-left"><i class="fa fa-comments"></i> <?php echo JText::sprintf( 'COM_CJBLOG_NUM_ARTICLES', $item->num_articles ); ?></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </li>
				<?php
			}
			?>
        </ul>
    </div>
	<?php
}
else
{
	?>
    <div class="alert alert-info"><?php echo JText::_( 'COM_CJBLOG_NO_RESULTS_FOUND' ); ?></div>
	<?php
}
