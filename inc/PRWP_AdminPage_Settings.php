<?php

class PRWP_AdminPage_Settings extends PRWP_AdminPage
{

    function __construct()
    {
        parent::__construct();
        $this->adminPage();
        $this->tabs = array(
            'general' => __('General', 'prwp'),
            'api' => __('APIs', 'prwp')
        );
        $this->page = 'prwp-settings';

        if (empty($this->tab)){
            $this->tab = 'general';
        }

    }

    function adminPageCallback(){
        $this->pagehook = add_menu_page(__( "Photos Recognition", 'prwp' ), __( "Photos Recognition", 'prwp' ), 'edit_themes', $this->page, array( $this, 'adminPageBody' ) );
    }

    function adminPageBody() {
        if ($this->isSavingData() && $this->getPage == $this->page) {
            $this->adminSaveData();
        }

        echo '<div class="wrap prwp-settings">';

        $this->adminPageHeading();
        $this->adminPageTabs();

        /*$this->notices($notices);*/

        echo '<form action="" method="post" class="validate" enctype="multipart/form-data">';
        echo '<input type="hidden" name="prwp" value="'.$this->page.'"/>';
        wp_nonce_field('prwp', 'prwp', false );

        $this->adminPageLayout();

        submit_button();
        echo '</form>';
        echo '</div>';
    }

    function adminPageHeading() {
        echo '<h2>'.__('Photos Recognition Settings','prwp').' (Alpha)</h2>';
    }

    function adminPageTabs() {
        $prefix = admin_url('admin.php');

        if (!empty($this->tabs)) {
            foreach ($this->tabs as $key => $tab) {
                if ($this->tab == $key) {
                    $active_class = 'nav-tab-active';
                } else {
                    $active_class = '';
                }
                $uri = $prefix . '?page=' . $this->page . '&tab=' . $key;
                $navtabs[] = '<a id="' . $key . '-tab" class="nav-tab ' . $active_class . '" title="' . $tab . '" href="' . $uri . '">' . $tab . '</a>';
            }
        }

        if(count($navtabs) > 1){
            echo '<h2 class="nav-tab-wrapper">'.implode($navtabs).'</h2>';
        }
    }

    function adminPageLayout(){

        $fields = new PRWP_AdminPage_Fields();
        $prefix = admin_url('admin.php');
        $apiuri = $prefix . '?page=' . $this->page . '&tab=api';


        if ($this->tab == 'general') {
            $fields->showTitle(__('Recognition Info','prwp'));
            $description = __('This plugin connects your WordPress with image recognition APIs and save locally the data retrieved from them, 
            for example it saves the recognized labels for a photo, and you can use them later for example as tags for the photo-description 
            or can be use by another plugin to add data to any place (like search engines, SEO configuration, or page text).','prwp');
            $fields->showDescription($description);
            $description = __('Feel free to use, edit, add APIs or ask me about this plugin. It\'s on current development and any help will be useful for the community.','prwp');
            $fields->showDescription($description);
            $description = __('In order to work, this plugin should be connected at least with one service, please check the <a href="'.$apiuri.'">APIs section</a> to activate them.','prwp');
            $fields->showDescription($description);

            $fields->showTableStart();
            $fields->showCheckbox('prwp-automatic', __('Automatic mode', 'prwp'), __('New uploaded images will be processed automatically', 'prwp'));
            $fields->showTableEnd();

        } else if ($this->tab == 'api'){
            $fields->showTitle(__('Amazon AWS Rekognition Settings','prwp'));
            $description = __('Amazon AWS Rekognition lets you use his service to do image recognition. Note that this service can incur in charges in your Amazon AWS Account.','prwp');
            $fields->showDescription($description);
            $description = __('¿How do i get API Keys? Easy (or not): 
                    <ul class="prwp-admin-ul">
                        <li>Go to your AWS Console, if you don\'t have an account, create one.</li>
                        <li>Go to IAM Control Panel (in AWS Services).</li>
                        <li>In the sidebar click "Users" and in this new page click "Add User"</li>
                        <li>Add a User Name you can remember, and check "Programmatic access". Click Next.</li>
                        <li>On Permissions click on "Attach existing policies directly" and Search and Check "AmazonRekognitionReadOnlyAccess". Click Next.</li>
                        <li>¿Are we sure?. Click Create User.</li>
                        <li>Copy the "Access key ID" and "Secret access key", that\'s we need</li>
                    </ul>
                    So we just added a new user to AWS with permissions to access Rekognition APIs','prwp');
            $fields->showDescription($description);

            $fields->showTableStart();
            $fields->showText('prwp-api-aws-key', __('AWS Access Key ID', 'prwp'));
            $fields->showText('prwp-api-aws-secret', __('AWS Secret access key', 'prwp'));
            $fields->showCheckbox('prwp-AWSRekognition-labels', __('Activate AWS Rekognition for Labels', 'prwp'), __('This will use Amazon AWS Labels recognition.', 'prwp'));

            $fields->showTableEnd();

            $fields->showTitle(__('Google Cloud Vision','prwp'));
            $description = __('Google Cloud Vision lets you use his service to do image recognition. Note that this service can incur in charges in your Google Cloud Account.','prwp');
            $fields->showDescription($description);
            $description = __('¿How do i get API Keys? Mmmmm ok for now we don\'t have an easy method to connect with Google Cloud, while our minions
            are working just create a json credentials file, put it in this plugin folder with the name "service-account.json", set permissions to disallow reading from outside and pray', 'prwp');
            $fields->showDescription($description);

            $fields->showTableStart();
            $fields->showText('prwp-gc-projectid', __('Google Cloud Project ID', 'prwp'));
            $fields->showCheckbox('prwp-GoogleCloudVision-labels', __('Activate Google Cloud Vision for Labels', 'prwp'), __('This will use Google Cloud Vision Labels recognition.', 'prwp'));

            $fields->showTableEnd();

        }




    }



    function adminSaveData(){
        $fields = new PRWP_AdminPage_Fields();

        if ($this->tab == 'general'){
            $fields->saveCheckbox('prwp-automatic');
        } else if ($this->tab == 'api'){
            $fields->saveText('prwp-api-aws-key');
            $fields->saveText('prwp-api-aws-secret');
            $fields->saveCheckbox('prwp-AWSRekognition-labels');
            $fields->saveText('prwp-gc-projectid');
            $fields->saveCheckbox('prwp-GoogleCloudVision-labels');

        }
    }

}