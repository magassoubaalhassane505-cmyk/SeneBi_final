<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // Affiche le tableau de bord de l'agriculteur (Sidi)
    public function clientDashboard() {
        $derniereVisite = Auth::user()->visites()->first();

        return view('client-dashboard', compact('derniereVisite'));
    }

    // Affiche le calculateur de rentabilité
    public function rentabilite() {
        return view('rentabilite');
    }

    // Affiche la gestion des parcelles
    public function parcelles() {
        $parcelles = Auth::user()->parcelles()->orderBy('nom')->get();

        return view('parcelles', compact('parcelles'));
    }

    // Affiche la gestion des stocks du client
    public function stocks() {
        app(\App\Http\Controllers\ClientApiController::class)->stocksIndex();
        $stocks = Stock::where('user_id', Auth::id())->orderBy('nom')->get();

        return view('stocks', compact('stocks'));
    }

    // Affiche le profil et les informations du compte
    public function compte() {
        return view('compte-client');
    }
}