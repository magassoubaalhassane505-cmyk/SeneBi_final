<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientApiController extends Controller
{
    public function parcellesIndex()
    {
        $parcelles = Auth::user()->parcelles()->orderBy('nom')->get();

        return response()->json(['data' => $parcelles]);
    }

    public function parcellesStore(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'surface' => 'required|numeric|min:0.01',
            'culture' => 'required|string|max:255',
            'statut' => 'nullable|string|max:100',
        ]);

        $parcelle = Auth::user()->parcelles()->create([
            'nom' => $data['nom'],
            'region' => $data['region'],
            'surface' => $data['surface'],
            'culture' => $data['culture'],
        ]);

        return response()->json(['data' => $parcelle], 201);
    }

    public function parcellesUpdate(Request $request, Parcelle $parcelle)
    {
        $this->authorizeParcelle($parcelle);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'region' => 'sometimes|string|max:255',
            'surface' => 'sometimes|numeric|min:0.01',
            'culture' => 'sometimes|string|max:255',
        ]);

        $parcelle->update($data);

        return response()->json(['data' => $parcelle->fresh()]);
    }

    public function parcellesDestroy(Parcelle $parcelle)
    {
        $this->authorizeParcelle($parcelle);
        $parcelle->delete();

        return response()->json(['ok' => true]);
    }

    public function stocksIndex()
    {
        $user = Auth::user();
        $this->ensureDefaultStocks($user);

        $stocks = Stock::where('user_id', $user->id)->orderBy('nom')->get();

        return response()->json(['data' => $stocks]);
    }

    public function stocksUpdate(Request $request, Stock $stock)
    {
        $this->authorizeStock($stock);

        $data = $request->validate([
            'quantite_actuelle' => 'required|numeric|min:0',
            'seuil_critique' => 'nullable|numeric|min:0',
            'cout_unitaire' => 'nullable|numeric|min:0',
        ]);

        $stock->update($data);

        return response()->json(['data' => $stock->fresh()]);
    }

    protected function authorizeParcelle(Parcelle $parcelle): void
    {
        if ($parcelle->user_id !== Auth::id()) {
            abort(403);
        }
    }

    protected function authorizeStock(Stock $stock): void
    {
        if ($stock->user_id !== Auth::id()) {
            abort(403);
        }
    }

    protected function ensureDefaultStocks($user): void
    {
        if (Stock::where('user_id', $user->id)->exists()) {
            return;
        }

        $defaults = [
            ['nom' => 'Urée', 'type' => 'Engrais', 'quantite_actuelle' => 520, 'seuil_critique' => 500, 'cout_unitaire' => 15000],
            ['nom' => 'NPK', 'type' => 'Engrais', 'quantite_actuelle' => 900, 'seuil_critique' => 450, 'cout_unitaire' => 18000],
            ['nom' => 'Semences', 'type' => 'Semence', 'quantite_actuelle' => 240, 'seuil_critique' => 100, 'cout_unitaire' => 800],
        ];

        foreach ($defaults as $row) {
            Stock::create([...$row, 'user_id' => $user->id]);
        }
    }
}
