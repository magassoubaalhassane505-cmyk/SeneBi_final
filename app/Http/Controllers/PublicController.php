<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.home');
    }

    public function solutions()
    {
        return view('public.solutions');
    }

    public function features()
    {
        return redirect('/solutions');
    }

    public function services()
    {
        return redirect('/solutions');
    }

    public function bi()
    {
        return redirect('/solutions');
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        return back()->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons sous peu.');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function login()
    {
        return view('public.login');
    }

    public function register()
    {
        return view('public.register');
    }
}
