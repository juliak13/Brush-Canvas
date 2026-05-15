<?
    function db_connect() {
        $result = new mysqli('localhost', 'konopkj1_chicken', 'chickens123', 'konopkj1_project');
        if (!$result) {
            return false;
        }
        $result->autocommit(TRUE);
        return $result;
    }

    function db_result_to_array($result) {
        $res_array = array();

        for ($count=0; $row = $result->fetch_assoc(); $count++) {
            $res_array[$count] = $row;
        }
        return $res_array;
    }
?>