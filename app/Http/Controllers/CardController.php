<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Jobs\ArchiveCard;
use Illuminate\Http\Request;
use App\Http\Resources\CardResource;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\returnSelf;

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
    public function store(StoreCardRequest $request)
    {
        $card = Card::create($request->validated());
        Log::info('cards.store',['id'=>$card->id]);

        return (new CardResource($card))
            ->response()
            ->setStatusCode(201)
            ->header('Location',route('cards.show',$card));
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        return new CardResource($card);
    }

    /**
     * Update the specified resource in storage.
     */
    // PATCH /cards/{card} → 200（本体返す） or 204（本体なし）
    public function update(Request $request, Card $card)
    {
        $card->update($request->validate());
        Log::info('cards.update',['id'=>$card->id]);

        return new CardResource($card);

    }

    /**
     * Remove the specified resource from storage.
     */
    // DELETE /cards/{card} → 204
    public function destroy(Card $card)
    {
        $card->delete();
        Log::info('cards.destroy', ['id' => $card->id]);

        return response()->noContent();
    }
}
