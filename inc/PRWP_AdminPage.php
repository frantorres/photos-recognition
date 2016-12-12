<?php

class PRWP_AdminPage
{

    function __construct()
    {
        $this->tab = (!empty($_GET['tab'])) ? $_GET['tab'] : '';
        $this->getPage = (!empty($_GET['page'])) ? $_GET['page'] : ''; //TODO Security check
        $this->pagehook = null;

    }

    function adminPage(){
        add_action('admin_menu', array($this, 'adminPageCallback'));
    }

    function isSavingData(){
        if (!empty($this->getPage)
            && isset( $_POST['prwp'] )
            && wp_verify_nonce( $_POST['prwp'], 'prwp' )){
            return true;
        }
        return false;
    }

}