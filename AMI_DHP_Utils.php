<?php

class AMI_DHP_Utils
{
    /** @var AMI_DHP_Utils */
    private static $instance = null;

    /** @var array */
    private $posts_deleted = array();

    /**
     * @return AMI_DHP_Utils
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Process the delete hidden posts request from the admin
     */
    public function delete_hidden_posts_process()
    {
        // load all required files if not loaded
        require_once QA_INCLUDE_DIR . 'app/admin.php';
        require_once QA_INCLUDE_DIR . 'db/admin.php';
        require_once QA_INCLUDE_DIR . 'db/selects.php';
        require_once QA_INCLUDE_DIR . 'app/format.php';

        //	Check admin privileges
        if (qa_user_maximum_permit_error('permit_hide_show') && qa_user_maximum_permit_error('permit_delete_hidden')) {
            return;
        }

        //	Find recently hidden questions, answers, comments
        list($hiddenquestions, $hiddenanswers, $hiddencomments) = qa_db_select_with_pending(
            qa_db_qs_selectspec(null, 'created', 0, null, null, 'Q_HIDDEN', true),
            qa_db_recent_a_qs_selectspec(null, 0, null, null, 'A_HIDDEN', true),
            qa_db_recent_c_qs_selectspec(null, 0, null, null, 'C_HIDDEN', true)
        );

        // first delete all hidden posts
        foreach ($hiddencomments as $hiddencomment) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($hiddencomment['opostid']);
        }

        // delete all the hidden answers
        foreach ($hiddenanswers as $hiddenanswer) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($hiddenanswer['opostid']);
        }

        // delete all the hidden questions
        foreach ($hiddenquestions as $hiddenquestion) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($hiddenquestion['postid']);
        }
    }

    /**
     * Fetches the child posts of the question and delete them recursively
     *
     * @param $postid
     */
    public function post_delete_recursive($postid)
    {
        require_once QA_INCLUDE_DIR . 'app/admin.php';
        require_once QA_INCLUDE_DIR . 'db/admin.php';
        require_once QA_INCLUDE_DIR . 'db/selects.php';
        require_once QA_INCLUDE_DIR . 'app/format.php';
        require_once QA_INCLUDE_DIR . 'app/posts.php';

        // For qa_cookie_get()
        require_once QA_INCLUDE_DIR . 'app/cookies.php';

        $loggedInUserId = qa_get_logged_in_userid();
        $loggedInHandle = qa_get_logged_in_handle();
        $loggedInCookie = qa_cookie_get();

        if (in_array($postid, $this->posts_deleted)) {
            return;
        }

        $oldpost = qa_post_get_full($postid, 'QAC');

        if (!$oldpost['hidden']) {
            qa_post_set_status($postid, QA_POST_STATUS_HIDDEN, $loggedInUserId);
            $oldpost = qa_post_get_full($postid, 'QAC');
        }

        switch ($oldpost['basetype']) {
            case 'Q':
                $answers = qa_post_get_question_answers($postid);
                $commentsfollows = qa_post_get_question_commentsfollows($postid);
                $closepost = qa_post_get_question_closepost($postid);

                foreach ($answers as $answer) {
                    AMI_DHP_Utils::getInstance()->post_delete_recursive($answer['postid']);
                }

                foreach ($commentsfollows as $commentsfollow) {
                    AMI_DHP_Utils::getInstance()->post_delete_recursive($commentsfollow['postid']);
                }

                if (!in_array($oldpost['postid'], $this->posts_deleted)) {
                    qa_question_delete($oldpost, $loggedInUserId, $loggedInHandle, $loggedInCookie, $closepost);
                    $this->posts_deleted[] = $oldpost['postid'];
                }
                break;

            case 'A':
                $question = qa_post_get_full($oldpost['parentid'], 'Q');
                $commentsfollows = qa_post_get_answer_commentsfollows($postid);

                foreach ($commentsfollows as $commentsfollow) {
                    AMI_DHP_Utils::getInstance()->post_delete_recursive($commentsfollow['postid']);
                }

                if (!in_array($oldpost['postid'], $this->posts_deleted)) {
                    qa_answer_delete($oldpost, $question, $loggedInUserId, $loggedInHandle, $loggedInCookie);
                    $this->posts_deleted[] = $oldpost['postid'];
                }
                break;

            case 'C':
                $parent = qa_post_get_full($oldpost['parentid'], 'QA');
                $question = qa_post_parent_to_question($parent);
                if (!in_array($oldpost['postid'], $this->posts_deleted)) {
                    qa_comment_delete($oldpost, $question, $parent, $loggedInUserId, $loggedInHandle, $loggedInCookie);
                    $this->posts_deleted[] = $oldpost['postid'];
                }
                break;
        }

    }

    /**
     * Adds delete button to the question
     *
     * @param $buttons
     * @param $post
     */
    public function add_q_delete_button(&$buttons, $post)
    {
        if (!$this->is_user_eligible_to_delete(qa_get_logged_in_userid(), isset($post['userid']) ? $post['userid'] : null) || isset($buttons['delete'])) {
            return;
        }

        $prefix = 'q' . $post['postid'] . '_';
        if (qa_clicked($prefix . AMI_DHP_Constants::DELETE_BTN)) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($post['postid']);
            qa_redirect('');
        } else {
            $buttons[AMI_DHP_Constants::DELETE_BTN] = array(
                'tags' => 'name="' . $prefix . AMI_DHP_Constants::DELETE_BTN . '" class="qa-form-light-button qa-form-light-button-delete" onclick="dhp_ask_user_confirmation(event);"',
                'label' => $this->lang('delete_q'),
                'popup' => qa_lang('question/delete_q_popup'),
            );
        }
    }

    /**
     * Adds delete button to the answer
     *
     * @param $buttons
     * @param $post
     */
    public function add_a_delete_button(&$buttons, $post)
    {
        if (!$this->is_user_eligible_to_delete(qa_get_logged_in_userid(), isset($post['userid']) ? $post['userid'] : null) || isset($buttons['delete'])) {
            return;
        }

        $prefix = 'a' . $post['postid'] . '_';

        if (qa_clicked($prefix . AMI_DHP_Constants::DELETE_BTN)) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($post['postid']);
            qa_redirect(qa_request());
        } else {
            $buttons[AMI_DHP_Constants::DELETE_BTN] = array(
                'tags' => 'name="' . $prefix . AMI_DHP_Constants::DELETE_BTN . '" class="qa-form-light-button qa-form-light-button-delete" onclick="dhp_ask_user_confirmation(event);"',
                'label' => $this->lang('delete_a'),
                'popup' => qa_lang('question/delete_a_popup'),
            );
        }
    }

    /**
     * Adds delete button to the comment
     *
     * @param $buttons
     * @param $post
     */
    public function add_c_delete_button(&$buttons, $post)
    {
        if (!$this->is_user_eligible_to_delete(qa_get_logged_in_userid(), isset($post['userid']) ? $post['userid'] : null) || isset($buttons['delete'])) {
            return;
        }

        $prefix = 'c' . $post['postid'] . '_';

        if (qa_clicked($prefix . AMI_DHP_Constants::DELETE_BTN)) {
            AMI_DHP_Utils::getInstance()->post_delete_recursive($post['postid']);
            qa_redirect(qa_request());
        } else {
            $buttons[AMI_DHP_Constants::DELETE_BTN] = array(
                'tags' => 'name="' . $prefix . AMI_DHP_Constants::DELETE_BTN . '" class="qa-form-light-button qa-form-light-button-delete" onclick="dhp_ask_user_confirmation(event);"',
                'label' => $this->lang('delete_c'),
                'popup' => qa_lang('question/delete_c_popup'),
            );
        }
    }

    /**
     * Checks if the user is eligible to delete the post
     *
     * @param null $userid if the userid is not passed uses loggedin userid
     * @param null $post_userid
     *
     * @return bool
     */
    public function is_user_eligible_to_delete($userid = null, $post_userid = null)
    {
        if (is_null($userid) || !isset($userid)) {
            // if the userid is not set then get the logged in userid
            $userid = qa_get_logged_in_userid();
        }

        if (is_null($userid) && !qa_is_logged_in()) {
            // if still it is null then ret false
            return false;
        }

        // return true for all special users that is allowed from admin panel
        if (qa_get_logged_in_level() >= qa_opt(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q)) {
            return true;
        }

        if (qa_opt(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA) && !is_null($post_userid) && ((int)$userid == (int)$post_userid)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the language value as defined in qa-dhp-lang-*.php
     *
     * @param $indentifier
     * @param null $subs
     *
     * @return mixed|string
     */
    public function lang($indentifier, $subs = null)
    {
        if (is_array($subs)) {
            return strtr(qa_lang('ami_dhp/' . $indentifier), $subs);
        }

        return empty($subs)
            ? qa_lang('ami_dhp/' . $indentifier)
            : qa_lang_sub('ami_dhp/' . $indentifier, $subs);
    }
}
