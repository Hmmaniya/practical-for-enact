<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $guarded = ['id'];
    protected $fillable=[
        'title',
        'probability',
        'awarded_count'
    ];
    
    public static function nextPrize()
    {
        $prizes = Prize::all();
        $totalProbability = $prizes->sum('probability');
        
        // Generate a random number between 0 and the total probability
        $random = mt_rand(0, $totalProbability * 100) / 100;
        $cumulativeProbability = 0;

        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize->probability;

            if ($random <= $cumulativeProbability) {
                // Increment the count of this prize being awarded
                $prize->increment('awarded_count');
                return $prize;
            }
        }

        return null;
    }
}
