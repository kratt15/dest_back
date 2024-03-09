<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class invoiceController extends Controller
{



    public function show($ref_purchase)
{
     $purchase = Purchase::where('ref_purchase', $ref_purchase)->first();

     $payments = Payment::join('purchases', 'payments.purchase_id', '=', 'purchases.id')
            ->where('purchases.ref_purchase', $ref_purchase)
            ->select('payments.*')
            ->orderByDesc('payments.created_at')->get();

      $totalAmountPaid  = 0;

      foreach ($payments as $payment) {
          $totalAmountPaid += $payment->amount;
      }



    $customer = Customer::find($purchase->customer_id);

    $name = $customer->name_customer;

    $phone = $customer->phone_customer;

    $address = $customer->address_customer;

    $items = $purchase->items()->withPivot('quantity')->get();

    $ref = $purchase->ref_purchase;

    $store_name = $purchase->store->name;
    $items_names = [];
    $quantities = [];
    $price = [];
    $total = [];

    foreach ($items as $item) {
        // Noms des articles
        $items_names[] = $item->name;

        // Quantités achetées
        $quantities[] = $item->pivot->quantity;

        $price[] = $item->price;

        $total[] = $item->price * $item->pivot->quantity;
    }

    $dueAmount = $items->sum(function ($item) {

        return $item->price * $item->pivot->quantity;

    });

    $leftOver = $dueAmount - $totalAmountPaid;


    // return view('invoices.invoice', compact('ref', 'items_names', 'quantities', 'price', 'total', 'dueAmount', 'name', 'phone', 'address', 'totalAmountPaid', 'leftOver'));
    $pdf= Pdf::loadView('invoices.invoice', compact('ref', 'items_names', 'quantities', 'price', 'total', 'dueAmount', 'name', 'phone', 'address','store_name' ,'totalAmountPaid', 'leftOver'));
    return $pdf->stream();
}



}
