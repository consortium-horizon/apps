<?php

defined('MBQ_IN_IT') or exit;

/**
 * user read class
 * 
 * @since  2012-8-6
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtUser extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return user api data
     *
     * @param  Object  $oMbqEtUser
     * @return  Array
     */
    public function returnApiDataUser($oMbqEtUser) {
        if (MbqMain::isJsonProtocol()) return $this->returnJsonApiDataUser($oMbqEtUser);
        $data = array();
        if ($oMbqEtUser->userId->hasSetOriValue()) {
            $data['user_id'] = (string) $oMbqEtUser->userId->oriValue;
        }
        $data['username'] = (string) $oMbqEtUser->getDisplayName();
        $data['user_name'] = (string) $oMbqEtUser->getDisplayName();
        if ($oMbqEtUser->userGroupIds->hasSetOriValue()) {
            $data['usergroup_id'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtUser->userGroupIds->oriValue);
        }
        if ($oMbqEtUser->iconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtUser->iconUrl->oriValue;
        }
        if ($oMbqEtUser->userEmail->hasSetOriValue()) {
            $data['email'] = (string) $oMbqEtUser->userEmail->oriValue;
        }
        if ($oMbqEtUser->postCount->hasSetOriValue()) {
            $data['post_count'] = (int) $oMbqEtUser->postCount->oriValue;
        }
        if ($oMbqEtUser->canPm->hasSetOriValue()) {
            $data['can_pm'] = (boolean) $oMbqEtUser->canPm->oriValue;
        } else {
            $data['can_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canPm.default');
        }
        if ($oMbqEtUser->canSendPm->hasSetOriValue()) {
            $data['can_send_pm'] = (boolean) $oMbqEtUser->canSendPm->oriValue;
        } else {
            $data['can_send_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSendPm.default');
        }
        if ($oMbqEtUser->canModerate->hasSetOriValue()) {
            $data['can_moderate'] = (boolean) $oMbqEtUser->canModerate->oriValue;
        } else {
            $data['can_moderate'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canModerate.default');
        }
        if ($oMbqEtUser->canSearch->hasSetOriValue()) {
            $data['can_search'] = (boolean) $oMbqEtUser->canSearch->oriValue;
        } else {
            $data['can_search'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSearch.default');
        }
        if ($oMbqEtUser->canWhosonline->hasSetOriValue()) {
            $data['can_whosonline'] = (boolean) $oMbqEtUser->canWhosonline->oriValue;
        } else {
            $data['can_whosonline'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canWhosonline.default');
        }
        if ($oMbqEtUser->canUploadAvatar->hasSetOriValue()) {
            $data['can_upload_avatar'] = (boolean) $oMbqEtUser->canUploadAvatar->oriValue;
        } else {
            $data['can_upload_avatar'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canUploadAvatar.default');
        }
        if ($oMbqEtUser->maxAttachment->hasSetOriValue()) {
            $data['max_attachment'] = (int) $oMbqEtUser->maxAttachment->oriValue;
        }
        if ($oMbqEtUser->maxPngSize->hasSetOriValue()) {
            $data['max_png_size'] = (int) $oMbqEtUser->maxPngSize->oriValue;
        }
        if ($oMbqEtUser->maxJpgSize->hasSetOriValue()) {
            $data['max_jpg_size'] = (int) $oMbqEtUser->maxJpgSize->oriValue;
        }
        if ($oMbqEtUser->displayText->hasSetOriValue()) {
            $data['display_text'] = (string) $oMbqEtUser->displayText->oriValue;
        }
        if ($oMbqEtUser->regTime->hasSetOriValue()) {
            $data['reg_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtUser->regTime->oriValue);
        }
        if ($oMbqEtUser->lastActivityTime->hasSetOriValue()) {
            $data['last_activity_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtUser->lastActivityTime->oriValue);
        }
        if ($oMbqEtUser->isOnline->hasSetOriValue()) {
            $data['is_online'] = (boolean) $oMbqEtUser->isOnline->oriValue;
        }
        if ($oMbqEtUser->acceptPm->hasSetOriValue()) {
            $data['accept_pm'] = (boolean) $oMbqEtUser->acceptPm->oriValue;
        } else {
            $data['accept_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.acceptPm.default');
        }
        if ($oMbqEtUser->iFollowU->hasSetOriValue()) {
            $data['i_follow_u'] = (boolean) $oMbqEtUser->iFollowU->oriValue;
        } else {
            $data['i_follow_u'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.iFollowU.default');
        }
        if ($oMbqEtUser->uFollowMe->hasSetOriValue()) {
            $data['u_follow_me'] = (boolean) $oMbqEtUser->uFollowMe->oriValue;
        } else {
            $data['u_follow_me'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.uFollowMe.default');
        }
        if ($oMbqEtUser->acceptFollow->hasSetOriValue()) {
            $data['accept_follow'] = (boolean) $oMbqEtUser->acceptFollow->oriValue;
        } else {
            $data['accept_follow'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.acceptFollow.default');
        }
        if ($oMbqEtUser->followingCount->hasSetOriValue()) {
            $data['following_count'] = (int) $oMbqEtUser->followingCount->oriValue;
        }
        if ($oMbqEtUser->follower->hasSetOriValue()) {
            $data['follower'] = (int) $oMbqEtUser->follower->oriValue;
        }
        if ($oMbqEtUser->currentAction->hasSetOriValue()) {
            $data['current_action'] = (string) $oMbqEtUser->currentAction->oriValue;
        }
        if ($oMbqEtUser->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtUser->topicId->oriValue;
        }
        if ($oMbqEtUser->canBan->hasSetOriValue()) {
            $data['can_ban'] = (boolean) $oMbqEtUser->canBan->oriValue;
        } else {
            $data['can_ban'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canBan.default');
        }
        if ($oMbqEtUser->isBan->hasSetOriValue()) {
            $data['is_ban'] = (boolean) $oMbqEtUser->isBan->oriValue;
        }
        if ($oMbqEtUser->canMarkSpam->hasSetOriValue()) {
            $data['can_mark_spam'] = (boolean) $oMbqEtUser->canMarkSpam->oriValue;
        } else {
            $data['can_mark_spam'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canMarkSpam.default');
        }
        if ($oMbqEtUser->isSpam->hasSetOriValue()) {
            $data['is_spam'] = (boolean) $oMbqEtUser->isSpam->oriValue;
        }
        if ($oMbqEtUser->reputation->hasSetOriValue()) {
            $data['reputation'] = (int) $oMbqEtUser->reputation->oriValue;
        }
        if ($oMbqEtUser->customFieldsList->hasSetOriValue()) {
            $data['custom_fields_list'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtUser->customFieldsList->oriValue);
        }
        
        if ($oMbqEtUser->postCountdown->hasSetOriValue()) {
            $data['post_countdown'] = (string) $oMbqEtUser->postCountdown->oriValue;
        }
        
        
        return $data;
    }
    public function returnJsonApiDataUser($oMbqEtUser) {
        $data = array();
        if ($oMbqEtUser->userId->hasSetOriValue()) {
            $data['user_id'] = (string) $oMbqEtUser->userId->oriValue;
        }
        $data['username'] = (string) $oMbqEtUser->getDisplayName();
        $data['user_name'] = (string) $oMbqEtUser->getDisplayName();
        if ($oMbqEtUser->userGroupIds->hasSetOriValue()) {
            $data['usergroup_id'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtUser->userGroupIds->oriValue);
        }
        if ($oMbqEtUser->iconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtUser->iconUrl->oriValue;
        }
        if ($oMbqEtUser->userEmail->hasSetOriValue()) {
            $data['email'] = (string) $oMbqEtUser->userEmail->oriValue;
        }
        if ($oMbqEtUser->postCount->hasSetOriValue()) {
            $data['post_count'] = (int) $oMbqEtUser->postCount->oriValue;
        }
        if ($oMbqEtUser->canPm->hasSetOriValue()) {
            $data['can_pm'] = (boolean) $oMbqEtUser->canPm->oriValue;
        } else {
            $data['can_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canPm.default');
        }
        if ($oMbqEtUser->canSendPm->hasSetOriValue()) {
            $data['can_send_pm'] = (boolean) $oMbqEtUser->canSendPm->oriValue;
        } else {
            $data['can_send_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSendPm.default');
        }
        if ($oMbqEtUser->canModerate->hasSetOriValue()) {
            $data['can_moderate'] = (boolean) $oMbqEtUser->canModerate->oriValue;
        } else {
            $data['can_moderate'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canModerate.default');
        }
        if ($oMbqEtUser->canSearch->hasSetOriValue()) {
            $data['can_search'] = (boolean) $oMbqEtUser->canSearch->oriValue;
        } else {
            $data['can_search'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canSearch.default');
        }
        if ($oMbqEtUser->canWhosonline->hasSetOriValue()) {
            $data['can_whosonline'] = (boolean) $oMbqEtUser->canWhosonline->oriValue;
        } else {
            $data['can_whosonline'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canWhosonline.default');
        }
        if ($oMbqEtUser->canUploadAvatar->hasSetOriValue()) {
            $data['can_upload_avatar'] = (boolean) $oMbqEtUser->canUploadAvatar->oriValue;
        } else {
            $data['can_upload_avatar'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canUploadAvatar.default');
        }
        if ($oMbqEtUser->maxAttachment->hasSetOriValue()) {
            $data['max_attachment'] = (int) $oMbqEtUser->maxAttachment->oriValue;
        }
        if ($oMbqEtUser->maxPngSize->hasSetOriValue()) {
            $data['max_png_size'] = (int) $oMbqEtUser->maxPngSize->oriValue;
        }
        if ($oMbqEtUser->maxJpgSize->hasSetOriValue()) {
            $data['max_jpg_size'] = (int) $oMbqEtUser->maxJpgSize->oriValue;
        }
        if ($oMbqEtUser->displayText->hasSetOriValue()) {
            $data['display_text'] = (string) $oMbqEtUser->displayText->oriValue;
        }
        if ($oMbqEtUser->regTime->hasSetOriValue()) {
            $data['reg_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtUser->regTime->oriValue);
        }
        if ($oMbqEtUser->lastActivityTime->hasSetOriValue()) {
            $data['last_activity_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtUser->lastActivityTime->oriValue);
        }
        if ($oMbqEtUser->isOnline->hasSetOriValue()) {
            $data['is_online'] = (boolean) $oMbqEtUser->isOnline->oriValue;
        }
        if ($oMbqEtUser->acceptPm->hasSetOriValue()) {
            $data['accept_pm'] = (boolean) $oMbqEtUser->acceptPm->oriValue;
        } else {
            $data['accept_pm'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.acceptPm.default');
        }
        if ($oMbqEtUser->iFollowU->hasSetOriValue()) {
            $data['i_follow_u'] = (boolean) $oMbqEtUser->iFollowU->oriValue;
        } else {
            $data['i_follow_u'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.iFollowU.default');
        }
        if ($oMbqEtUser->uFollowMe->hasSetOriValue()) {
            $data['u_follow_me'] = (boolean) $oMbqEtUser->uFollowMe->oriValue;
        } else {
            $data['u_follow_me'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.uFollowMe.default');
        }
        if ($oMbqEtUser->acceptFollow->hasSetOriValue()) {
            $data['accept_follow'] = (boolean) $oMbqEtUser->acceptFollow->oriValue;
        } else {
            $data['accept_follow'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.acceptFollow.default');
        }
        if ($oMbqEtUser->followingCount->hasSetOriValue()) {
            $data['following_count'] = (int) $oMbqEtUser->followingCount->oriValue;
        }
        if ($oMbqEtUser->follower->hasSetOriValue()) {
            $data['follower'] = (int) $oMbqEtUser->follower->oriValue;
        }
        if ($oMbqEtUser->currentAction->hasSetOriValue()) {
            $data['current_action'] = (string) $oMbqEtUser->currentAction->oriValue;
        }
        if ($oMbqEtUser->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtUser->topicId->oriValue;
        }
        if ($oMbqEtUser->canBan->hasSetOriValue()) {
            $data['can_ban'] = (boolean) $oMbqEtUser->canBan->oriValue;
        } else {
            $data['can_ban'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canBan.default');
        }
        if ($oMbqEtUser->isBan->hasSetOriValue()) {
            $data['is_ban'] = (boolean) $oMbqEtUser->isBan->oriValue;
        }
        if ($oMbqEtUser->canMarkSpam->hasSetOriValue()) {
            $data['can_mark_spam'] = (boolean) $oMbqEtUser->canMarkSpam->oriValue;
        } else {
            $data['can_mark_spam'] = (boolean) MbqBaseFdt::getFdt('MbqFdtUser.MbqEtUser.canMarkSpam.default');
        }
        if ($oMbqEtUser->isSpam->hasSetOriValue()) {
            $data['is_spam'] = (boolean) $oMbqEtUser->isSpam->oriValue;
        }
        if ($oMbqEtUser->reputation->hasSetOriValue()) {
            $data['reputation'] = (int) $oMbqEtUser->reputation->oriValue;
        }
        if ($oMbqEtUser->customFieldsList->hasSetOriValue()) {
            $data['custom_fields_list'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtUser->customFieldsList->oriValue);
        }
        
        if ($oMbqEtUser->postCountdown->hasSetOriValue()) {
            $data['post_countdown'] = (string) $oMbqEtUser->postCountdown->oriValue;
        }
        
        
        return $data;
    }
    /**
     * return user json api data
     *
     * @param  Object  $oMbqEtUser
     * @return  Array
     */
    protected function returnAdvJsonApiDataUser($oMbqEtUser) {
        $data = array();
        if ($oMbqEtUser->userId->hasSetOriValue()) {
            $data['id'] = (string) $oMbqEtUser->userId->oriValue;
        }
        $data['name'] = (string) $oMbqEtUser->getDisplayName();
        if ($oMbqEtUser->iconUrl->hasSetOriValue()) {
            $data['avatar'] = (string) $oMbqEtUser->iconUrl->oriValue;
        }
        if ($oMbqEtUser->isOnline->hasSetOriValue()) {
            $data['online'] = (boolean) $oMbqEtUser->isOnline->oriValue;
        }
        return $data;
    }
    
    /**
     * return user array api data
     *
     * @param  Array  $objsMbqEtUser
     * @param  Boolean  $forceHash mark whether return hash data
     * @return  Array
     */
    public function returnApiArrDataUser($objsMbqEtUser, $forceHash = false) {
        $data = array();
        foreach ($objsMbqEtUser as $oMbqEtUser) {
            if ($forceHash) {
                $data[$oMbqEtUser->userId->oriValue] = $this->returnApiDataUser($oMbqEtUser);
            } else {
                $data[] = $this->returnApiDataUser($oMbqEtUser);
            }
        }
        return $data;
    }
    
    /**
     * login
     *
     * @return  Boolean  return true when login success.
     */
    public function login() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * logout
     *
     * @return  Boolean  return true when logout success.
     */
    public function logout() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get user objs
     *
     * @return  Array
     */
    public function getObjsMbqEtUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one user by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init current user obj if login
     */
    public function initOCurMbqEtUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get user display name
     *
     * @return  String
     */
    public function getDisplayName() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
    
    /**
    * sign in
    *
    * @return Array
    */
    public function signIn() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    /**
    * login directly without password
    * only used for sign_in method
    *
    * @return Boolean
    */
    protected function loginDirectly() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }

    /**
    * forget_password
    *
    * @return Array
    */
    public function forgetPassword() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    /**
    * judge is admin role
    *
    * @return Boolean
    */
    public function isAdminRole() {
    
        
    }
    
}

?>