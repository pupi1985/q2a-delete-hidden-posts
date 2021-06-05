<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function q_view_buttons($q_view)
    {
        if (isset($q_view['form']['buttons']) && count($q_view['form']['buttons'])) {
            ami_dhp_add_q_delete_button($q_view['form']['buttons'], $q_view['raw']);
        }

        parent::q_view_buttons($q_view);
    }

    function a_item_buttons($a_item)
    {
        if (isset($a_item['form']['buttons']) && count($a_item['form']['buttons'])) {
            ami_dhp_add_a_delete_button($a_item['form']['buttons'], $a_item['raw']);
        }

        parent::a_item_buttons($a_item);
    }

    function c_item_buttons($c_item)
    {
        if (isset($c_item['form']['buttons']) && count($c_item['form']['buttons'])) {
            ami_dhp_add_c_delete_button($c_item['form']['buttons'], $c_item['raw']);
        }

        parent::c_item_buttons($c_item);
    }

    function head_script()
    {
        parent::head_script();

        if (!ami_dhp_is_user_eligible_to_delete()) {
            return;
        }

        $html =
            '<script>' .
            'function dhp_ask_user_confirmation(event){' .
            'if (!confirm("' . dhp_lang('are_you_sure') . '")) {' .
            'event.preventDefault();' .
            'return false;' .
            '}' .
            'return true;' .
            '}' .
            '</script>';

        echo $html;
    }
}
