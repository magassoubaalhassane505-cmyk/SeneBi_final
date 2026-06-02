<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ManagementController extends Controller
{
    // Affiche la liste des visites de terrain (Fichier: visits-control.blade.php)
    public function visites() {
        return view('visits-control');
    }

    // Affiche la gestion des stocks d'intrants (Fichier: stocks.blade.php)
    public function stocks() {
        return view('stocks');
    }

    // NOUVEAU : Affiche le catalogue des prix (Fichier: catalogue.blade.php)
    public function catalogue() {
        return view('catalogue');
    }

    // NOUVEAU : Affiche la supervision des agriculteurs (Fichier: supervision.blade.php)
    public function supervision() {
        $pendingClients = collect();

        if (Schema::hasColumn('users', 'is_active')) {
            $pendingClients = User::where('role', 'client')
                ->where('is_active', false)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $activityLogs = collect();
        if (Schema::hasTable('activity_logs')) {
            $activityLogs = ActivityLog::with(['actor', 'targetUser'])
                ->latest()
                ->take(25)
                ->get();
        }

        return view('supervision', compact('pendingClients', 'activityLogs'));
    }

    public function approveClient(User $user)
    {
        if ($user->role !== 'client') {
            return redirect()->back();
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            return redirect()->route('manager.supervision')->with('status', 'Le champ is_active est manquant. Veuillez exécuter la migration.');
        }

        $payload = [
            'is_active' => true,
            'status' => 'approved',
            'rejection_reason' => null,
            'rejected_at' => null,
        ];

        if (Schema::hasColumn('users', 'approved_at')) {
            $payload['approved_at'] = now();
        }
        if (Schema::hasColumn('users', 'approved_by')) {
            $payload['approved_by'] = auth()->id();
        }

        $user->update($payload);

        ActivityLog::record(
            'client.approved',
            $user->id,
            'Compte approuvé par ' . (auth()->user()->name ?? 'manager')
        );

        return redirect()->route('manager.supervision')->with(
            'status',
            'Le compte de ' . $user->name . ' a bien été approuvé. Il peut se connecter avec son email '
            . $user->email . ' et le mot de passe choisi lors de l\'inscription.'
        );
    }

    public function rejectClient(Request $request, User $user)
    {
        if ($user->role !== 'client') {
            return redirect()->back();
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reason = $request->reason ?? 'Compte rejeté par l\'administrateur.';

        $payload = [
            'is_active' => false,
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ];

        if (Schema::hasColumn('users', 'rejected_at')) {
            $payload['rejected_at'] = now();
        }
        if (Schema::hasColumn('users', 'approved_at')) {
            $payload['approved_at'] = null;
        }
        if (Schema::hasColumn('users', 'approved_by')) {
            $payload['approved_by'] = null;
        }

        $user->update($payload);

        ActivityLog::record(
            'client.rejected',
            $user->id,
            $reason
        );

        return redirect()->route('manager.supervision')->with('status', 'Le compte de ' . $user->name . ' a bien été rejeté.');
    }

    // NOUVEAU : Affiche le compte manager (Fichier: compte.blade.php)
    public function compte() {
        return view('compte');
    }

    // Dans app/Http/Controllers/ManagementController.php
}