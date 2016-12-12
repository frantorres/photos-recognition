<?php


class PRWP_AdminPage_Fields
{

    function showTitle($title){
        ?>
        <h3><?php echo $title; ?></h3>
        <?php
    }

    function showDescription($description){
        ?>
        <p><?php echo $description; ?></p>
        <?php
    }

    function showTableStart(){
        echo '<table class="form-table"><tbody>';
    }

    function showTableEnd(){
        echo '</tbody></table>';
    }

    function showText($option, $title, $description=''){
        if(!$value=get_option($option)){
            $value = '';
        }
        ?>
        <tr>
            <th scope="row"><label for="<?php echo $option; ?>"><?php echo $title; ?></label></th>
            <td><input name="<?php echo $option; ?>" type="text" id="<?php echo $option; ?>" value="<?php echo $value; ?>" class="regular-text">
                <?php if(!empty($description)): ?>
                <p class="description"><?php echo $description; ?></p></td>
                <?php endif; ?>
        </tr>
        <?php
    }

    function saveText($option){
        $value = (!empty($_POST[$option])) ? sanitize_text_field($_POST[$option]) : 0;
        update_option($option,$value);
    }

    function showCheckbox($option, $title, $description=''){
        ?>
        <tr>
            <th scope="row"><?php echo $title; ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo $title; ?></span></legend>
                    <label for="<?php echo $option; ?>">
                        <?php
                        if (get_option($option)) {
                            $checked = 'checked';
                        } else {
                            $checked = '';
                        }
                        ?>
                        <input name="<?php echo $option; ?>" type="checkbox" id="<?php echo $option; ?>"
                               value="1" <?php echo $checked; ?>>
                        <?php echo $description; ?>
                    </label>
                </fieldset>
            </td>
        </tr>

        <?php
    }

    function saveCheckbox($option){
        $value = (!empty($_POST[$option])) ? 1 : 0;
        update_option($option,$value);
    }

}