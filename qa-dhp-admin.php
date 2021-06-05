<?php

class qa_dhp_admin
{
    const SAVE_BTN = 'ami_dhp_save_button';
    const DELETE_HIDDEN_POSTS_BTN = 'ami_dhp_delete_button';

    function option_default($option)
    {
        switch ($option) {
            case AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA :
                return 1;
            case AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q :
                return QA_USER_LEVEL_ADMIN;
            default:
                return null;
        }
    }

    function admin_form(&$qa_content)
    {
        $ok = null;
        if (qa_clicked(self::SAVE_BTN)) {
            qa_opt(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA, (bool)qa_post_text(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA));
            qa_opt(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q, (int)qa_post_text(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q));
            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked(self::DELETE_HIDDEN_POSTS_BTN)) {
            $ok = AMI_DHP_Utils::getInstance()->dhp_lang('all_hidden_posts_deleted');
            AMI_DHP_Utils::getInstance()->ami_dhp_delete_hidden_posts_process();
        }

        //	Create the form for display_header_text();

        $user_levels = array(
            QA_USER_LEVEL_EXPERT => qa_lang('users/level_expert'),
            QA_USER_LEVEL_EDITOR => qa_lang('users/level_editor'),
            QA_USER_LEVEL_MODERATOR => qa_lang('users/level_moderator'),
            QA_USER_LEVEL_ADMIN => qa_lang('users/level_admin'),
            QA_USER_LEVEL_SUPER => qa_lang('users/level_super'),
        );

        $fields = array();

        $fields[AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA] = array(
            'id' => AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA,
            'label' => AMI_DHP_Utils::getInstance()->dhp_lang('same_user_can_delete'),
            'type' => 'checkbox',
            'value' => qa_opt(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA),
            'tags' => 'NAME="' . AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA . '"',
        );

        $fields[AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q] = array(
            'id' => AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q,
            'label' => AMI_DHP_Utils::getInstance()->dhp_lang('choose_who_can_delete_all'),
            'type' => 'select',
            'value' => $user_levels[qa_opt(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q)],
            'options' => $user_levels,
            'tags' => 'NAME="' . AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q . '"',
        );

        return array(
            'ok' => ($ok) ? $ok : null,

            'fields' => $fields,

            'buttons' => array(
                array(
                    'label' => qa_lang_html('main/save_button'),
                    'tags' => 'NAME="' . self::SAVE_BTN . '"',
                ),
                array(
                    'label' => AMI_DHP_Utils::getInstance()->dhp_lang('delete_hidden_posts'),
                    'tags' => 'NAME="' . self::DELETE_HIDDEN_POSTS_BTN . '" onclick="dhp_ask_user_confirmation(event) && qa_show_waiting_after(this, false);"',
                ),
            ),
        );
    }
}
