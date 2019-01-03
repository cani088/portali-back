<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;
use App\Models\Comment;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(){
        $comment=Comment::all()->random(1);
        return $comment[0]->comment_id;
        $b=DB::table('comments')->get();
        $b=$b->each(function($o){
            $o->bes='asdasdas';
        })->reject(function($a){
            return $a->comment_id>3;
        });

        $b->dump();
    }
}
