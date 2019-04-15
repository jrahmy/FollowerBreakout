<?php

/*
 * This file is part of a XenForo add-on.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jrahmy\FollowerBreakout\XF\Entity;

/**
 * Extends \XF\Entity\UserFollow
 */
class UserFollow extends XFCP_UserFollow
{
    /**
     * @noparent
     */
    protected function _preSave()
    {
        if ($this->isInsert()) {
            if ($this->user_id == $this->follow_user_id) {
                $this->error(\XF::phrase('you_may_not_follow_yourself'));
            }

            $exists = $this->em()->findOne('XF:UserFollow', [
                'user_id'        => $this->user_id,
                'follow_user_id' => $this->follow_user_id
            ]);
            if ($exists) {
                $this->error(\XF::phrase('you_already_following_this_member'));
            }

            /** @var \XF\Mvc\Entity\Finder $followFinder */
            $followFinder = $this->finder('XF:UserFollow');
            $followFinder->where('user_id', $this->user_id);

            $total = $followFinder->total();
            $followLimit = 1000;
            if ($total == $followLimit) {
                $this->error(\XF::phrase('you_may_only_follow_x_people', [
                    'count' => $followLimit
                ]));
            }
        }
    }
}
