<?php

use Faker\Generator as Faker;

$factory->define(\jhoopes\LaravelVueForms\Models\FormConfiguration::class, function(Faker $faker) {
    return [
        'name' => $faker->word,
        'type' => $faker->word,
        'active' => $faker->randomElement([1, 0]),
        'entity_name' => $faker->word,
        'entity_type' => $faker->className,
    ];
});
