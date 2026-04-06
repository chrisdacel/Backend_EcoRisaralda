<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuristicPlace;
use App\Models\User;
use App\Models\reviews;


class AdminFunctionController extends Controller
{
    public function index()
    {
        $counts = [
            'users' => User::count(),
            'places' => TuristicPlace::count(),
            'reviews' => reviews::count(),
        ];
        return view('admin.panel_control', compact('counts'));
    }
    public function manageUsers()
    {
        $users = User::all();
        return view('admin.manage_users', compact('users'));
    }
    public function deleteUser()
    {
        $id=request()->input('id');
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.manage_users')->with('success', 'User deleted successfully.');
    }
    public function managereviews()
    {
        
        $reviews = reviews::all();
        return view('admin.manage_reviews', compact('reviews'));
    }
    public function deleteReview(){
        $id=request()->input('id');
        $review = reviews::findOrFail($id);
        $review->delete();
        return redirect()->route('admin.manage_reviews')->with('success', 'Review deleted successfully.');
    }
}

