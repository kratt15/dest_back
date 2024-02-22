<?php

namespace App\Http\Controllers\Api;

use App\Mail\TestMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    //
    public function testMail()
    {
        $to = 'sidneyadjoh15@gmail.com';
        $subject = 'Test Email';
        $message = 'This is a test email from Laravel.';

        Mail::to($to)->send(new TestMail($subject, $message));

        return 'Test email sent!';
    }
}
