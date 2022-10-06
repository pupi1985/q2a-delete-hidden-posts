<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function q_view_buttons($q_view)
    {
        if (!empty($q_view['form']['buttons']) && !empty($q_view['raw'])) {
            AMI_DHP_Utils::getInstance()->add_q_delete_button($q_view['form']['buttons'], $q_view['raw']);
        }

        parent::q_view_buttons($q_view);
    }

    function a_item_buttons($a_item)
    {
        if (!empty($a_item['form']['buttons']) && !empty($a_item['raw'])) {
            AMI_DHP_Utils::getInstance()->add_a_delete_button($a_item['form']['buttons'], $a_item['raw']);
        }

        parent::a_item_buttons($a_item);
    }

    function c_item_buttons($c_item)
    {
        if (!empty($c_item['form']['buttons']) && !empty($c_item['raw'])) {
            AMI_DHP_Utils::getInstance()->add_c_delete_button($c_item['form']['buttons'], $c_item['raw']);
        }

        parent::c_item_buttons($c_item);
    }

    function head_script()
    {
        parent::head_script();

        if (!AMI_DHP_Utils::getInstance()->is_user_eligible_to_delete()) {
            return;
        }

        $html =
            '<script>' .
            'function dhp_ask_user_confirmation(event){' .
            'if (!confirm("' . AMI_DHP_Utils::getInstance()->lang('are_you_sure') . '")) {' .
            'event.preventDefault();' .
            'return false;' .
            '}' .
            'return true;' .
            '}' .
            '</script>';

        echo $html;
    }
}
