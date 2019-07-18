<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Hero;
use App\Enemy;

class BSController extends Controller
{
    public function index(){
        $hero = Hero::find(5)->first();
        $enemy = Enemy::find(2)->first();

        $events = [];

        while($hero->hp > 0 && $enemy->hp > 0){
            $luck = random_int(0, 100);

            if($luck >= 50){
                $hp = $enemy->def - $hero->atq;

                if($hp < 0){
                    $enemy->hp -= $hp * -1;
                }

                if($enemy->hp > 0){
                    $ev = [
                        "winner" => "hero",
                        "text" => $hero->name . " hizo un daño de " . $hero->atq . " a " . $enemy->name
                    ];
                } else {
                    $ev = [
                        "winner" => "hero",
                        "text" => $hero->name . " acabó con la vida de " . $enemy->name . " y gano " . $enemy->xp . " de experiencia."
                    ];

                    $hero->xp = $hero->xp + $enemy->xp;

                    if($hero->xp >= $hero->level->xp){
                        $hero->xp = 0;
                        $hero->level_id += 1;
                    }

                    $hero->save();
                }
            } else {
                $hp = $hero->def - $enemy->atq;

                if($hp < 0){
                    $hero->hp -= $hp * -1;
                }

                if($hero->hp > 0){
                    $ev = [
                        "winner" => "enemy",
                        "text" => $hero->name . " recibio un daño de " . $enemy->atq . " por " . $enemy->name
                    ];
                } else {
                    $ev = [
                        "winner" => "enemy",
                        "text" => $hero->name . " fue asesinado por " . $enemy->name
                    ];
                }
            }

            array_push($events, $ev);
        }

        return view('admin.bs.index', [
            "events" => $events,
            "heroName" => $hero->name,
            "enemyName" => $enemy->name
            ]);
    }
}
