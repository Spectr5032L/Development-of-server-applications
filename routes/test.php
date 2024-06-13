<?php
    $tasks = [1, 2, 3, 4, 5, 6, 8, 9, 10, 14, 15, 17];

    function getRandomTask($tasks) {
        $randomIndex = array_rand($tasks);
        return $tasks[$randomIndex];
    }

    $randomTask = getRandomTask($tasks);
    echo "Случайно выбранная задача: $randomTask";
?>