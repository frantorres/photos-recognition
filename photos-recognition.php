<?php
/**
 * Plugin Name: Photos Recognition
 * Description: Let's discover what's on your uploaded Photos. This Plugins connects with an external service (currently just Amazon AWS) to help you tagging your images.
 * Plugin URI: https://giga4.es
 * Author: frantorres
 * Author URI: http://frantorres.es
 * Version: 0.1.alpha
 * Text Domain: prwp
 * License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'PRWP_VERSION', '0.1' );
define( 'PRWP_PATH', dirname( __FILE__ ) );
define( 'PRWP_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'PRWP_FOLDER', basename( PRWP_PATH ) );
define( 'PRWP_URL', plugins_url() . '/' . PRWP_FOLDER );
define( 'PRWP_URL_INCLUDES', PRWP_URL . '/inc' );
global $prwp_adminErrors_message;
$prwp_adminErrors_message=array();


include (PRWP_PATH_INCLUDES.'/PRWP_MediaInterface.php');
include (PRWP_PATH_INCLUDES.'/PRWP_AdminPage.php');
include (PRWP_PATH_INCLUDES.'/PRWP_AdminPage_Fields.php');
include (PRWP_PATH_INCLUDES.'/PRWP_AdminPage_Settings.php');

include (PRWP_PATH_INCLUDES.'/PRWP_API.php');
include (PRWP_PATH_INCLUDES.'/PRWP_Labels.php');

include (PRWP_PATH_INCLUDES.'/PRWP_Events.php');


class PRWP_Base {

    public function __construct() {

        $this->registerScripts();
        $this->loadPluginTextDomain();

        $adminPageSettings = new PRWP_AdminPage_Settings();
        $mediaInterface = new PRWP_MediaInterface();

        $events = new PRWP_Events();


    }

    function registerScripts(){
        add_action( 'admin_enqueue_scripts', array( $this, 'registerJSadminScripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'registerCSSadminScripts' ) );
    }


    public function registerJSadminScripts( $hook ) {
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'prwp-admin', plugins_url( '/js/prwp-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
        wp_enqueue_script( 'prwp-admin' );
    }

    public function registerCSSadminScripts( $hook ) {
        wp_register_style( 'prwp-admin', plugins_url( '/css/prwp-admin.css', __FILE__ ), array(), '1.0', 'screen' );
        wp_enqueue_style( 'prwp-admin' );

    }

    public function loadPluginTextDomain() {
        add_action( 'plugins_loaded', array( $this, 'loadPluginTextDomainCallBack' ) );
    }
    public function loadPluginTextDomainCallBack() {
        load_plugin_textdomain( 'prwp', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }


}

function prwp_methodToString($method){
    if ($method=='AWSRekognition'){
        return __('Amazon AWS Rekognition','prwp');
    }

    if ($method=='GoogleCloudVision'){
        return __('Google Cloud Vision','prwp');
    }
}

function prwp_adminErrorsAdd($message){
    global $prwp_adminErrors_message;
    $prwp_adminErrors_message[]=$message;
}

add_action( 'admin_notices', 'prwp_adminErrorsShow');
function prwp_adminErrorsShow(){
    global $prwp_adminErrors_message;
    foreach ($prwp_adminErrors_message as $message) {
        $class = 'notice notice-error';
        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
    }
}

// Initialize everything
$PRWP_plugin_base = new PRWP_Base();