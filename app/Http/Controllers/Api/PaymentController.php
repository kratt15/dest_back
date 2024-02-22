<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentFormRequest;

class PaymentController extends Controller
{
    // Liste des payements
    public function list(): JsonResponse
    {
        $payments = Payment::orderByDesc('created_at')->get();

        return response()->json([$payments]);
    }
    public function listByRefPurchase($ref_purchase): JsonResponse
    {
        $payments = Payment::join('purchases', 'payments.purchase_id', '=', 'purchases.id')
            ->where('purchases.ref_purchase', $ref_purchase)
            ->select('payments.*')
            ->orderByDesc('payments.created_at')->get();


        $purchase = Purchase::where('ref_purchase', $ref_purchase)->first();
        $dueAmount = 0;
        $items = $purchase->items()->get();
        foreach ($items as $item) {
            $item_price = $item->price;
            $item_qte = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
            $amount = $item_price * $item_qte;
            $dueAmount += $amount;
        }
        return response()->json([
            'payments' => $payments,
            'montantAchat' => $dueAmount
        ]);
    }

    // Ajout de payement
    public function store(PaymentFormRequest $request): JsonResponse
    {
        //Récupuératon d'achat
        $purchase = Purchase::where('ref_purchase', $request->ref_purchase)->first();

        //recupérer la quantité achetée
        $dueAmount = 0;
        $items = $purchase->items()->get();
        foreach ($items as $item) {
            $item_price = $item->price;
            $item_qte = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
            $amount = $item_price * $item_qte;
            $dueAmount += $amount;
        }

        $paidAmount = 0;
        $payments = $purchase->payments()->get();
        if ($payments->count() > 0) {
            foreach ($payments as $payment) {
                $payment_amount = $payment->amount;
                $paidAmount += $payment_amount;
            }
        } else {
            $paidAmount = 0;
        }

        if ($request->amount <= ($dueAmount - $paidAmount)) {

            $reference = uniqid();
            // Creer un nouveau payement
            $payment = Payment::create([
                'ref_payment' => $reference,
                'amount' => $request->amount,
                'payment_date_time' => date('Y-m-d H:i:s'),
            ]);

            // Associer l'achat au payement
            $payment->purchase()->associate($purchase);
            $payment->save();

            return response()->json(['message' => "Payement créé avec succès !", 'payment' => $payment], 201);
        } else {
            return response()->json(['error' => " votre payement est superieur au montant de l'achat"], 400);
        }
    }

    // Mise à jour de payement
    // public function update(PaymentFormRequest $request, Payment $payment): JsonResponse
    // {
    //     // Si le payement n'existe pas, renvoyer une page not found
    //     if (!$payment) {
    //         return response()->json(['error' => "Payement non trouvé !"], 404);
    //     }

    //     // Modifier le payement
    //     $payment->update([

    //         'amount' => $request->amount,
    //     ]);

    //     return response()->json(['message' => "Payement modifié avec succès !", 'payment' => $payment], 200);
    // }

    public function update(PaymentFormRequest $request, Payment $payment): JsonResponse
    {
        // If the payment does not exist, return a not found error
        if (!$payment) {
            return response()->json(['error' => "Payment not found!"], 404);
        }

        // Get the associated purchase
        $purchase = $payment->purchase;

        // Recalculate the due amount
        $dueAmount = 0;
        $items = $purchase->items()->get();
        foreach ($items as $item) {
            $item_price = $item->price;
            $item_qte = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
            $amount = $item_price * $item_qte;
            $dueAmount += $amount;
        }

        // Calculate the paid amount
        $paidAmount = $purchase->payments()->sum('amount');

        if ($request->amount <= ($dueAmount - $paidAmount)) {
            // Update the payment
            $payment->update([
                'amount' => $request->amount,
            ]);

            return response()->json(['message' => "Payment updated successfully!", 'payment' => $payment], 200);
        } else {
            return response()->json(['error' => "Your payment exceeds the purchase amount"], 400);
        }
    }

    // Suppression de payement
    public function delete(Payment $payment): JsonResponse
    {
        // Si le payement n'existe pas, renvoyer une page not found
        if (!$payment) {
            return response()->json(['error' => "Payement non trouvé !"], 404);
        }

        // Suppression logique
        $payment->delete();

        // Dissocier l'achat
        $payment->purchase()->dissociate();
        $payment->save();

        return response()->json(['message' => "Payement supprimé avec succès !"], 200);
    }
}
