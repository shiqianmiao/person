<?php

if (!function_exists('privilege_dfs')) {
    function privilege_dfs($key, $value) {
        if (isset($value['id'])) {
            echo '<li id="' . $value['id'] . '">';
            echo '<a href="#">' . $key . ' - ' . $value['name'] . '</a>';
            echo '</li>';
            return;
        }
        echo '<li>';
        echo '<a href="#" style="color: #0088cc;">' . $key . '</a>';
        echo '<ul>';
        foreach ($value as $k => $v) {
            privilege_dfs($k, $v);
        }
        echo '</ul>';
        echo '</li>';
    }
}

?>

<ul>
    <li>
        <a href="#" style="color: #0088cc;"><?php echo $this -> app; ?>.corp.273.cn</a>
        <ul>
            <?php
            foreach ($this->trieArray as $key => $value) {
                privilege_dfs($key, $value);
            }
            ?>
        </ul>
    </li>
</ul>