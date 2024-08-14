<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;


class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();
        return view('prizes.index', [
            'prizes' => $prizes,
            'isSimulated' => false,
        ]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $current_probability = floatval(Prize::sum('probability'));
        $remaining_probability = 100 - $current_probability;

        $submitted_probability = floatval($request->input('probability'));

        if ($submitted_probability > $remaining_probability) {
            return redirect()->back()->withErrors([
                'probability' => "The probability field must not be greater than $remaining_probability%. Currently, it's $submitted_probability%."
            ])->withInput();
        }

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = $request->input('probability');
        $prize->save();

        return to_route('prizes.index');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $current_probability = floatval(Prize::where('id', '!=', $id)->sum('probability'));
        $remaining_probability = 100 - $current_probability;

        $submitted_probability = floatval($request->input('probability'));

        if ($submitted_probability > $remaining_probability) {
            return redirect()->back()->withErrors([
                'probability' => "The probability field must not be greater than $remaining_probability%. Currently, it's $submitted_probability%."
            ])->withInput();
        }

        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = $request->input('probability');
        $prize->save();

        return to_route('prizes.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }
    public function simulate(Request $request) {
        $prizes = Prize::all();  // Fetch all prizes
        $entries = $request->input('entries', 100);  // Number of simulations
        
        $awardedCounts = array_fill_keys($prizes->pluck('id')->toArray(), 0);
    
        for ($i = 0; $i < $entries; $i++) {
            $randomNumber = mt_rand(1, 10000) / 100;  
            $cumulativeProbability = 0;
    
            foreach ($prizes as $prize) {
                $cumulativeProbability += $prize->probability;
                if ($randomNumber <= $cumulativeProbability) {
                    $awardedCounts[$prize->id]++;
                    break;
                }
            }
        }
        foreach ($awardedCounts as $prizeId => $count) {
            Prize::where('id', $prizeId)->update(['awarded_count' => (int) $count]);
        }
    
        $prizes = Prize::all();
    
        return view('prizes.index', [
            'prizes' => $prizes,
            'isSimulated' => true,
        ]);
    }
    
                
        public function reset() {
            Prize::query()->update(['awarded_count' => 0]);
            return redirect()->route('prizes.index');
        }
            
        }
