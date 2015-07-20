<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtForum extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtForum' => array(
            'parentRootForumId' => -1,   /* if parent fourm is root forum,then return this value */
            
            'newPost' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isProtected' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isSubscribed' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canSubscribe' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'subOnly' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canPost' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'unreadStickyCount' => array(
                'default' => 0
            ),
            'unreadAnnounceCount' => array(
                'default' => 0
            ),
            'requirePrefix' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canUpload' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        ),
        'MbqEtForumTopic' => array(
            'state' => array(
                'range' => array(
                    'postOk' => 0,
                    'postOkNeedModeration' => 1
                )
            ),
            'isSubscribed' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canSubscribe' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isClosed' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'newPost' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canThank' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canLike' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isLiked' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canDelete' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isDeleted' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canApprove' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isApproved' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canStick' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isSticky' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canClose' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canRename' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canMove' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isMoved' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canReply' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canReport' => array(
                'default' => true,  //for dummy report post
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isHot' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isDigest' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        ),
        'MbqEtForumPost' => array(
            'state' => array(
                'range' => array(
                    'postOk' => 0,
                    'postOkNeedModeration' => 1
                )
            ),
            'isOnline' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canEdit' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canDelete' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'allowSmilies' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canThank' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canLike' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isLiked' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isThanked' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canDelete' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isDeleted' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canApprove' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isApproved' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canMove' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canReport' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canUnlike' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canUnthank' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtForum'] = &MbqFdtForum::$df;

?>