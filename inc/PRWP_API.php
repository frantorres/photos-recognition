<?php

//Composer loading Google and AWS Libraries
require PRWP_PATH.'/vendor/autoload.php';
use Google\Cloud\Vision\VisionClient;

class PRWP_photosRecognitionAPI
{

    public function __construct(){
        $this->photoInfo=array();
        $this->photoInfo['labels']=array();
        $this->labels=null;
        $this->imgID=null;

    }

    function setImage($attachmentID){
        $this->imgID = $attachmentID;
        $this->labels = new PRWP_Labels($attachmentID);
    }

    function loadLabels(){

        //Get recognition methods and delete already used.
        $unusedMethods = $this->labels->getUnusedRecognitionMethods();
        if (!empty($unusedMethods)) {
            if ($imgFOpen = $this->loadImage()) {
                $this->getImageLabelsFromRecognition($imgFOpen);
                $this->saveImageLabels();
            }

        }

    }

    function saveImageLabels(){
        $this->labels->saveLabels();
    }

    function loadImage(){
        $imgPath= get_attached_file($this->imgID);
        $imgFOpen = fopen($imgPath, 'r'); //TODO check file exists
        return $imgFOpen;
    }

    function getImageLabelsFromRecognition($imgFOpen){
        $unusedMethods = $this->labels->getUnusedRecognitionMethods();

        if (in_array('AWSRekognition', $unusedMethods)) {
            $this->getImageLabelsAWSRekognition($imgFOpen);
        }

        if (in_array('GoogleCloudVision', $unusedMethods)){
            $this->getImageLabelsGoogleCloudVision($imgFOpen);
        }
    }

    function getImageLabelsAWSRekognition($imgFOpen){

        //TODO Check IMG
        //TODO This operation requires permissions to perform the rekognition:DetectLabels action.
        //TODO Blob of image bytes up to 5 MBs.
        //TODO Try catch
        $RClient = $this->AWSCreateClient();
        if (!empty($RClient)) {

            $imgStream = GuzzleHttp\Psr7\stream_for($imgFOpen);
            try {
                $result = $RClient->detectLabels([
                    'Image' => [
                        'Bytes' => $imgStream,
                    ],
                    'MaxLabels' => 50,
                    'MinConfidence' => 20,
                ]);
            } catch (Aws\Rekognition\Exception\RekognitionException $e) {
                prwp_adminErrorsAdd('There were an error with Amazon AWS Rekognition service. <br/>'.$e);
            }

            if (!empty($result)) {
                foreach ($result->get('Labels') as $label) {
                    if (!empty($label['Name']) && !empty($label['Confidence'])) {
                        $this->labels->addLabel($label['Name'], ($label['Confidence'] / 100), 'AWSRekognition');
                    }
                }
            }

            $this->labels->addRecognitionMethod('AWSRekognition');

        }


    }

    function AWSCreateClient(){
        if (!empty(get_option('prwp-api-aws-key'))){
            $apikey = get_option('prwp-api-aws-key');
        } else {
            return 0;
        }

        if (!empty(get_option('prwp-api-aws-secret'))){
            $apisecret = get_option('prwp-api-aws-secret');
        } else {
            return 0;
        }

        $RClient = new Aws\Rekognition\RekognitionClient([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => [
                'key'    => $apikey,
                'secret' => $apisecret,
            ],
        ]);
        return $RClient;
    }


    function getImageLabelsGoogleCloudVision($imgFOpen){

        //TODO Check IMG
        $vision = $this->GCCreateClient();
        if (!empty($vision)) {

            $image = $vision->image(
                $imgFOpen,
                ['LABEL_DETECTION']
            );

            $annotation = $vision->annotate($image);

            foreach ($annotation->info() as $key => $info) {
                foreach ($info as $label) {
                    if (!empty($label['description']) && !empty($label['score'])) {
                        $this->labels->addLabel($label['description'], $label['score'], 'GoogleCloudVision');
                    }
                }
            }

            $this->labels->addRecognitionMethod('GoogleCloudVision');

        }

    }

    function GCCreateClient(){
        if (!empty(get_option('prwp-gc-projectid'))){
            $projectid = get_option('prwp-gc-projectid');
        } else {
            return 0;
        }

        putenv('GOOGLE_APPLICATION_CREDENTIALS='.PRWP_PATH.'/service-account.json'); // TODO VAR

        try {
            $vision = new VisionClient([
                'projectId' => $projectid
            ]);
        } catch (DomainException $e) {
            prwp_adminErrorsAdd('Image Recognition: There were an error with Google Vision Cloud Authentication. <br/>'.$e);
            return 0;
        }

        return $vision;
    }

}