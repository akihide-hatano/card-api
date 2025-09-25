<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Jobs\ArchiveCard;
use Illuminate\Http\Request;
use App\Http\Resources\CardResource;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //Get /cards/->200
    public function index(Request $request)
    {
        Log::debug('cards.index',['q'=>$request->query('q')]);
        $q = $request->query('q');

        $query = Card::query()->latest('id');
        if($q){
                $query->where(function($w) use ($q) {
                $w->where('title','like',"%$q%")
                    ->orWhere('description','like',"%$q%");
        });
    }
        return CardResource::collection(
            $query->paginate(10)->appends($request->only('q')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        //
    }
}
