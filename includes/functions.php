<?php

 function message($msg, $pagina="http://localhost:8080/prenota2/index.php",$time="1000",$header='Yes'){
        if($header =="Yes"){
            echo "<script>
                alert('".$msg."');
                setTimeout(function() {
                window.location.href = '".$pagina."';
                }, $time);
            </script>";
            exit;
        }elseif($header =='No'){
            echo "<script>
            alert('".$msg."');
            </script>";
            exit;
        }else{
            echo "<script>
            alert('header non valido!');
            </script>";
            exit;
        }
       
    }

   function verificaData($dateString) {
        // Converto la stringa in oggetto DateTime
        $inputDate = new DateTime($dateString);
        
        // Data odierna senza orario
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        // Confronto
        return $inputDate > $today;
    }

?>