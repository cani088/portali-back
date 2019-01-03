<?php

use Faker\Generator as Faker;
use App\Models\Comment;
use App\Models\Article;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Comment::class, function (Faker $faker) {
    $comment=Comment::all()->random(1);
    $article=Article::all()->random(1);
    return [
        'user_id' => '2',
        'parent' => $comment[0]->comment_id,
        'depth' => $comment[0]->depth+1,
        'created_at_t'=>time(),
        'comment_body'=>'Dicka random',
        'article_id'=>$article[0]->article_id
    ];
});
