<?php

    class qa_dhp_admin
    {

        const SAVE_BTN                = 'ami_dhp_save_button';
        const DELETE_HIDDEN_POSTS_BTN = 'ami_dhp_delete_button';

        function option_default($option)
        {
            switch ($option) {
                case AMI_DHP_Constants::PLUGIN_ENABLED :
                case AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA :
                    return 1;
                    break;
                case AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q :
                    return QA_USER_LEVEL_ADMIN;
                default:
                    return null;
            }
        }

        function admin_form(&$qa_content)
        {

            //	Process form input

            $ok = null;
            if (qa_clicked(self::SAVE_BTN)) {
                qa_opt(AMI_DHP_Constants::PLUGIN_ENABLED, (bool) qa_post_text(AMI_DHP_Constants::PLUGIN_ENABLED));
                qa_opt(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA, (bool) qa_post_text(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA));
                qa_opt(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q, (int) qa_post_text(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q));
                $ok = qa_lang('admin/options_saved');
            } elseif (qa_clicked(self::DELETE_HIDDEN_POSTS_BTN)) {
                $ok = dhp_lang('all_hidden_posts_deleted');
                ami_dhp_delete_hidden_posts_process();
            }

            //	Create the form for display_header_text();

            $user_levels = array(
                QA_USER_LEVEL_EXPERT    => qa_lang('users/level_expert'),
                QA_USER_LEVEL_EDITOR    => qa_lang('users/level_editor'),
                QA_USER_LEVEL_MODERATOR => qa_lang('users/level_moderator'),
                QA_USER_LEVEL_ADMIN     => qa_lang('users/level_admin'),
                QA_USER_LEVEL_SUPER     => qa_lang('users/level_super'),
            );

            $fields = array();

            $fields[ AMI_DHP_Constants::PLUGIN_ENABLED ] = array(
                'label' => dhp_lang('dhp_enable'),
                'tags'  => 'NAME="' . AMI_DHP_Constants::PLUGIN_ENABLED . '" onClick=""',
                'value' => qa_opt(AMI_DHP_Constants::PLUGIN_ENABLED),
                'type'  => 'checkbox',
            );


            $fields[ AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA ] = array(
                'id'    => AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA,
                'label' => dhp_lang('same_user_can_delete'),
                'type'  => 'checkbox',
                'value' => qa_opt(AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA),
                'tags'  => 'NAME="' . AMI_DHP_Constants::SAME_USER_CAN_DELETE_QA . '"',
            );

            $fields[ AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q ] = array(
                'id'      => AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q,
                'label'   => dhp_lang('choose_who_can_delete_all'),
                'type'    => 'select',
                'value'   => $user_levels[ qa_opt(AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q) ],
                'options' => $user_levels,
                'tags'    => 'NAME="' . AMI_DHP_Constants::MIN_LEVEL_TO_DELETE_Q . '"',
            );

            return array(
                'ok'      => ($ok) ? $ok : null,

                'fields'  => $fields,

                'buttons' => array(
                    array(
                        'label' => qa_lang_html('main/save_button'),
                        'tags'  => 'NAME="' . self::SAVE_BTN . '"',
                    ),
                    array(
                        'label' => dhp_lang('delete_hidden_posts'),
                        'tags'  => 'NAME="' . self::DELETE_HIDDEN_POSTS_BTN . '" onclick="dhp_ask_user_confirmation(event) && qa_show_waiting_after(this, false);"',
                    ),

                ),
            );
        }
    }
