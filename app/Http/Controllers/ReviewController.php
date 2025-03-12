<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Enregistre un nouvel avis
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        // Vérifier si l'utilisateur a déjà laissé un avis pour ce restaurant
        $existingReview = Review::where('user_id', auth()->id())
            ->where('restaurant_id', $validated['restaurant_id'])
            ->first();

        if ($existingReview) {
            // Mettre à jour l'avis existant
            $existingReview->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            return redirect()->back()->with('success', 'Votre avis a été mis à jour avec succès.');
        }

        // Créer un nouvel avis
        Review::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $validated['restaurant_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'Votre avis a été publié avec succès.');
    }

    /**
     * Supprime un avis
     */
    public function destroy(Review $review)
    {
        // Vérifier que l'utilisateur est autorisé à supprimer cet avis
        $this->authorize('delete', $review);

        $review->delete();

        return redirect()->back()->with('success', 'Votre avis a été supprimé avec succès.');
    }
}
