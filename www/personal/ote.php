<?php
        include ("../include/funciones/funciones.php");
        $cnn = ConectarPersonal();
        $query_ot = "SELECT OE_ID FROM OT_EXTRA WHERE OE_ESTA = 'CANCELADO POR USUARIO' OR OE_ESTA = 'CANCELADO POR ADMIN' OR OE_ESTA = 'RECHAZADO J.D.'";
        $respuesta_ot = mysqli_query($cnn, $query_ot);
        echo $query_ot;
        while ($row = mysqli_fetch_array($respuesta_ot, MYSQLI_NUM)){;
            $oe_id = $row[0];
            $actualizarOTE = "UPDATE OTE_DETALLE SET OTE_ESTA = 'INACTIVO' WHERE OE_ID = $oe_id";
            mysqli_query($cnn,$actualizarOTE);
            echo $actualizarOTE;
            echo "<br>";
        }

?>