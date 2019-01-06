<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;
use App\Models\Comment;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(){
        
        $days_available=[
            'monday'=>0,
            'tuesday'=>0,
            'wednesday'=>0,
            'thursday'=>0,
            'friday'=>0,
            'saturday'=>0,
            'sunday'=>0
        ];

        $open_dates=[];
        $closed_dates=[];
    
        $prices=DB::table('prices')
            ->join('options','options.option_id','prices.selected_option')
            ->where('prices.offer_id',1)
            ->get();

        $min_date=$prices[0]->start_date;
        $max_date=$prices[0]->end_date;

        foreach($prices as &$p){

            //get days available
            foreach($days_available as $k=>$v){
                if($p->{$k}==1){
                    $days_available[$k]=1;
                }
            }

            //get min date
            if($p->start_date<$min_date && $p->start_date>time()){  
                $min_date=$p->start_date;
            }

            //max date
            if($p->end_date>$max_date){
                $max_date=$p->end_date;
            }

            //insert open dates
            foreach(explode(',',$p->open_dates) as $od){
                if(!in_array($od,$open_dates)){
                    $open_dates[]=$od;
                }
            }

            //insert closed dates
            foreach(explode(',',$p->closed_dates) as $cd){
                if(!in_array($od,$closed_dates)){
                    $closed_dates[]=$cd;
                }
            }
        }
        
        return [
            'days_available'=>$days_available,
            'min_date'=>$min_date,
            'max_date'=>$max_date,
            'open_dates'=>$open_dates,
            'closed_dates'=>$closed_dates
        ];

        return $prices;
    }
}
