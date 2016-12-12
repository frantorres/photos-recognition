<?php


class PRWP_Labels
{

    public function __construct($imgID){
        $this->imgID=$imgID;
        $this->labels=array();
        $this->labelsMethods=array();
        $this->recognitionMethods = array('GoogleCloudVision','AWSRekognition');
        $this->activeRecognitionMethods=array();
        $this->loadLabels();
    }

    function loadLabels(){
        $labels = get_post_meta($this->imgID,'PRWP_LabelsInfo', true);
        if (!empty($labels)){
            $labels = unserialize($labels);
            if (!empty($labels[0]['label'])){
                $this->labels=$labels;
            }
        }

        $labelsMethods = get_post_meta($this->imgID,'PRWP_LabelsMethods', true);
        if (!empty($labelsMethods)){
            $labelsMethods = unserialize($labelsMethods);
            if (!empty($labelsMethods[0])){
                $this->labelsMethods=$labelsMethods;
            }
        }
    }

    function activeRecognitionMethods(){
        foreach ($this->recognitionMethods as $recognitionMethod){
            if(!empty(get_option('prwp-'.$recognitionMethod.'-labels'))){
                $this->activeRecognitionMethods[]=$recognitionMethod;
            }
        }
    }

    function getUnusedRecognitionMethods(){
        $this->activeRecognitionMethods();
        $recognitionMethodsList = $this->activeRecognitionMethods;
        $usedRecognitionMethods=$this->labelsMethods;
        foreach ($usedRecognitionMethods as $usedRecognitionMethod) {
            if (($key = array_search($usedRecognitionMethod, $recognitionMethodsList)) !== false) {
                unset($recognitionMethodsList[$key]);
            }
        }
        return $recognitionMethodsList;
    }

    function addLabel($label, $score, $method){
        $label=strtolower($label);
        $found = false;

        foreach ($this->labels as $key => $labelObject){
            if ($labelObject['label']==$label){
                if (!in_array($method, $labelObject['method'])){
                    $this->labels[$key]['method'][]=$method;
                    //Set Max Score between methods
                    if ($labelObject['score']<$score){
                        $this->labels[$key]['score']=$score;
                    }
                }
                $found = true;
                break;
            }
        }

        if (!$found){
            $this->labels[]=array(
                'label' => $label,
                'score' => $score,
                'method' => array($method)
            );
        }

    }

    function addRecognitionMethod($method){
        if (!in_array($method, $this->labelsMethods)){
            $this->labelsMethods[]=$method;
        }
    }

    function orderLabels(){
        usort($this->labels, array( $this, 'orderLabelsSort' ));

    }

    function orderLabelsSort($a, $b){
        if ($a['score'] == $b['score']) {
            return 0;
        }
        return ($a['score'] < $b['score']) ? 1 : -1;
    }

    function saveLabels(){
        $this->orderLabels();
        $labelsSerialized = serialize($this->labels);
        update_post_meta($this->imgID,'PRWP_LabelsInfo', $labelsSerialized );

        $labelsMethodsSerialized = serialize($this->labelsMethods);
        update_post_meta($this->imgID,'PRWP_LabelsMethods', $labelsMethodsSerialized);
    }

    function showLabelsHtmlUl(){
        if (!empty($this->labels)) {
            echo '<ul>';
            foreach ($this->labels as $label) {
                $score = number_format($label['score'] * 100, 1);
                echo '<li>';
                echo $label['label'];
                echo ' (' . $score . '%)';
                foreach ($label['method'] as $method) {
                    echo '<i class="prwp-method ' . $method . '" title="' . prwp_methodToString($method) . '"></i>';
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo __('There are not labels, do you want to','prwp').' <a href="#" class="prwp-ajax-labelsHtmlUl-link">'.__('load them now?','prwp').'</a>';
        }

    }

    function getUsedRecognitionMethods(){
        return $this->labelsMethods;
    }

}