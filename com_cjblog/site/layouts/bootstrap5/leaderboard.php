<?php
/**
 * @package     CjBlog
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2023 BulaSikku Technologies Pvt. Ltd. All rights reserved.
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
    <div class="card card-default">
        <div class="card-header"><?php echo JText::_( 'COM_CJBLOG_LEADERBOARD' ); ?></div>
        <ul class="list-group no-space-left">
			<?php
			foreach ( $items as $rank => $item )
			{
				$author     = $this->escape( $item->$displayName );
				$profileUrl = $api->getUserProfileUrl( $profileComponent, $item->id );
				$userAvatar = $api->getUserAvatarImage( $avatar, $item->id, $item->email, 64, true );
				?>
                <li class="list-group-item no-margin-left">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="card card-success leader-rank-box" style="min-width: 75px; min-height: 72px;">
                                <h4 class="leader-rank text-center"><?php echo $rank + 1; ?></h4>
                                <div class="text-muted text-center"><?php echo JText::_( 'COM_CJBLOG_RANK_LABEL' ); ?></div>
                            </div>
                        </div>

						<?php if ( $avatar != 'none' ): ?>
                            <div class="flex-shrink-0 d-none d-md-block">
								<?php if ( $profileComponent != 'none' ): ?>
                                    <a href="<?php echo $profileUrl; ?>" title="<?php echo $author ?>" class="thumbnail" data-toggle="tooltip">
                                        <img class="img-thumbnail" src="<?php echo $userAvatar; ?>" alt="<?php echo $author; ?>" style="max-width: 64px;">
                                    </a>
								<?php else: ?>
                                    <div class="thumbnail">
                                        <img class="img-thumbnail" src="<?php echo $userAvatar; ?>" alt="<?php echo $author; ?>" style="max-width: 64px;">
                                    </div>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
                        <div class="flex-grow-1 ms-3">
                            <h4><?php echo $author; ?></h4>
                            <div class="text-muted">
                                <ul class="list-unstyled list-inline">
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
