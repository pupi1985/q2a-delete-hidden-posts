<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

require_once 'AMI_DHP_Constants.php';
require_once 'AMI_DHP_Utils.php';

qa_register_plugin_module('module', 'qa-dhp-admin.php', 'qa_dhp_admin', 'Delete Hidden Posts Admin');
qa_register_plugin_layer('qa-dhp-layer.php', 'Delete Hidden Posts Layer');
qa_register_plugin_phrases('lang/qa-dhp-lang-*.php', 'ami_dhp');
