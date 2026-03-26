<?php
function showErrors($errors){
    if(!empty($errors)){
        echo "<div class='alert alert-danger'>";
        foreach($errors as $e){
            echo "<div>$e</div>";
        }
        echo "</div>";
    }
}