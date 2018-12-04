<?php

/** http://php.net/manual/en/function.class-uses.php */
function class_uses_deep($class, $autoload = true) {
    $traits = [];

    // Get all the traits of $class and its parent classes
    do {
        $class_name = is_object($class)? get_class($class): $class;
        if (class_exists($class_name, $autoload)) {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        }
    } while ($class = get_parent_class($class));

    // Get traits of all parent traits
    $traits_to_search = $traits;
    while (!empty($traits_to_search)) {
        $new_traits = class_uses(array_pop($traits_to_search), $autoload);
        $traits = array_merge($new_traits, $traits);
        $traits_to_search = array_merge($new_traits, $traits_to_search);
    };

    return array_unique($traits);
}