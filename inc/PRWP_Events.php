<?php

//TODO Async load : eventNewUpload:
// -call to remote URL or Ajax
// -Set flag "loading"
// -Check if there's new info stored (showLabelsHtmlUl) every X seconds.


class PRWP_Events
{

    public function __construct(){
        $this->imgID=null;
        $this->activateOnNewUploads = get_option('prwp-automatic') ? 1 : 0;
        if ($this->activateOnNewUploads){
            add_action( 'add_attachment', array( $this, 'eventNewUpload' ) , 10, 1 );
        }

        add_action( 'wp_ajax_prwp_showLabels', array( $this,'showLabelsAjaxAction') );
        add_action( 'wp_ajax_prwp_generateLabels', array( $this,'generateLabelsAjaxAction') );

    }

    function eventNewUpload($post_id){
        $this->loadLabels($post_id);
    }

    function showLabelsAjaxAction() {
        $post_id = $_POST['postid'];
        if (!empty($post_id)){
            $labels = new PRWP_Labels($post_id);
            $labels->showLabelsHtmlUl();
        } else {
            echo "Error";
        }
        wp_die();
    }

    function generateLabelsAjaxAction() {
        $post_id = $_POST['postid'];
        if (!empty($post_id) && $this->loadLabels($post_id)){
            $labels = new PRWP_Labels($post_id);
            $labels->showLabelsHtmlUl();
        } else {
            echo "Error";
        }
        wp_die();
    }

    function loadLabels($post_id){
        $recognitionAPI = new PRWP_photosRecognitionAPI();
        $recognitionAPI->setImage($post_id);
        $recognitionAPI->loadLabels();
        return 1;
    }






}