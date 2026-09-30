<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SiteVisitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\AuditLogController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HOME / LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/login');
});

Route::get('/client/login', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return view('client.client-login');
});

Route::get('/client/register', function () {
    return view('client.client-register');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Password Reset Routes
    Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');

    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::post('/login', function (Request $req) {
        $validated = $req->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('email', $validated['email'])->first();

        $passwordMatches = false;

if ($user) {
   $passwordMatches = false;

if (
    str_starts_with($user->password, '$2y$') ||
    str_starts_with($user->password, '$2a$')
) {
    $passwordMatches = Hash::check(
        $validated['password'],
        $user->password
    );
} else {
    $passwordMatches =
        $validated['password'] === $user->password;
}
}

if (! $user || ! $passwordMatches) {
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}

        Auth::login($user);
        $req->session()->regenerate();
        session([
            'role' => $user->role
        ]);
        $redirectRoutes = [
            'admin' => '/dashboard',
            'agent' => '/dashboard',
            'accountant' => '/dashboard',
            'client' => '/client/dashboard',
        ];

        $redirectUrl = $redirectRoutes[$user->role] ?? '/dashboard';

        return redirect()->intended($redirectUrl);
    });

    Route::post('/register/store', function (Request $req) {
        $validated = $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
        ]);

        Auth::login($user);
        $req->session()->regenerate();

        return redirect('/client/dashboard');
    });
});

Route::post('/logout', function (Request $req) {
    Auth::logout();
    $req->session()->invalidate();
    $req->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN / CRM ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,agent,accountant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/create', function () {
        return view('add-client');
    });
    Route::post('/clients/store', [ClientController::class, 'store']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::get('/clients/{id}/edit', [ClientController::class, 'edit']);
    Route::post('/clients/{id}/update', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
});

Route::middleware(['auth', 'role:admin,agent'])->group(function () {
    Route::get('/deal-details/{deal}', [DealController::class, 'show']);
    Route::get('/deal-update/{deal}', [DealController::class, 'edit']);

    // Deals: add create page and store route (keeps existing resource routes intact)
    Route::get('/deals/create', [DealController::class, 'create']);
    Route::post('/deals/store', [DealController::class, 'store']);

    Route::resource('deals', DealController::class)->except(['create', 'show', 'edit']);

    // Offer & Negotiation Records routes
    Route::get('/deals/{deal_id}/negotiations/create', [App\Http\Controllers\NegotiationController::class, 'create']);
    Route::post('/deals/{deal_id}/negotiations/store', [App\Http\Controllers\NegotiationController::class, 'store']);
    Route::get('/negotiations/{id}/edit', [App\Http\Controllers\NegotiationController::class, 'edit']);
    Route::post('/negotiations/{id}/update', [App\Http\Controllers\NegotiationController::class, 'update']);
    Route::get('/negotiations/{id}/delete', [App\Http\Controllers\NegotiationController::class, 'destroy']);
    Route::delete('/negotiations/{id}', [App\Http\Controllers\NegotiationController::class, 'destroy']);

    // Agreement & Document Management routes
    Route::get('/documents/upload', [App\Http\Controllers\DocumentController::class, 'create']);
    Route::post('/documents/store', [App\Http\Controllers\DocumentController::class, 'store']);
    Route::get('/documents/{id}/download', [App\Http\Controllers\DocumentController::class, 'download']);
    Route::get('/documents/{id}/delete', [App\Http\Controllers\DocumentController::class, 'destroy']);
    Route::delete('/documents/{id}', [App\Http\Controllers\DocumentController::class, 'destroy']);

    // Booking & Sale Confirmation routes
    Route::post('/deals/{id}/confirm-booking', [DealController::class, 'confirmBooking']);
    Route::post('/deals/{id}/cancel-booking', [DealController::class, 'cancelBooking']);
    Route::post('/deals/{id}/confirm-sale', [DealController::class, 'confirmSale']);
    Route::post('/deals/{id}/cancel-sale', [DealController::class, 'cancelSale']);

    // Payment Tracking routes
    Route::post('/deals/{id}/record-advance', [DealController::class, 'recordAdvance']);
    Route::post('/deals/{id}/record-final', [DealController::class, 'recordFinal']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/create', function () {
        return view('add-property');
    });
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::post('/properties/store', [PropertyController::class, 'store']);
    Route::get('/properties/edit/{id}', [PropertyController::class, 'edit']);
    Route::post('/properties/{id}/update', [PropertyController::class, 'update']);
    Route::get('/properties/delete/{id}', [PropertyController::class, 'destroy']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);

    Route::get('/users/create', function () {
        return view('add-user');
    });

    Route::post('/users/store', [UserController::class, 'store']);
    Route::get('/users/{user}/edit', [UserController::class, 'edit']);

Route::put('/users/{user}', [UserController::class, 'update']);

Route::delete('/users/{user}', [UserController::class, 'destroy']);

Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});

Route::middleware(['auth', 'role:admin,accountant'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index']);
    Route::get('/reports/export', [ReportsController::class, 'exportPdf']);
});

Route::middleware(['auth', 'role:admin,agent'])->group(function () {
    Route::resource('leads', LeadController::class)->except(['show']);
    
    // Reminders routes
    Route::get('/reminders', [App\Http\Controllers\ReminderController::class, 'index']);
    Route::get('/reminders/create', [App\Http\Controllers\ReminderController::class, 'create']);
    Route::post('/reminders/store', [App\Http\Controllers\ReminderController::class, 'store']);
    Route::get('/reminders/{id}/edit', [App\Http\Controllers\ReminderController::class, 'edit']);
    Route::post('/reminders/{id}/update', [App\Http\Controllers\ReminderController::class, 'update']);
    Route::get('/reminders/{id}/delete', [App\Http\Controllers\ReminderController::class, 'destroy']);
    Route::delete('/reminders/{id}', [App\Http\Controllers\ReminderController::class, 'destroy']);
    Route::post('/reminders/{id}/complete', [App\Http\Controllers\ReminderController::class, 'markComplete']);
    Route::get('/reminders/{id}/complete', [App\Http\Controllers\ReminderController::class, 'markComplete']);
    Route::get('/reminders/{id}/send-email', [App\Http\Controllers\ReminderController::class, 'sendEmailNotification']);

    Route::resource('site-visits', SiteVisitController::class)->except(['show']);
    Route::get('/visit/create', [SiteVisitController::class, 'create']);
    Route::post('/visit/store', [SiteVisitController::class, 'store']);

    Route::get('/leads/{id}/contact', function ($id) {
        return "Contact Lead " . $id;
    });

    Route::get('/leads/{id}/follow', function ($id) {
        return "Follow Up Lead " . $id;
    });

    Route::get('/leads/{id}/schedule', function ($id) {
        return "Schedule Lead " . $id;
    });

    Route::get('/leads/{id}/update', function ($id) {
        return "Update Lead " . $id;
    });

    Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{id}/view', [LeadController::class, 'show']);

    // Communications routes
    Route::get('/communications', [App\Http\Controllers\CommunicationController::class, 'index']);
    Route::get('/communications/create', [App\Http\Controllers\CommunicationController::class, 'create']);
    Route::post('/communications/store', [App\Http\Controllers\CommunicationController::class, 'store']);
    Route::get('/communications/{id}/edit', [App\Http\Controllers\CommunicationController::class, 'edit']);
    Route::post('/communications/{id}/update', [App\Http\Controllers\CommunicationController::class, 'update']);
    Route::get('/communications/{id}/delete', [App\Http\Controllers\CommunicationController::class, 'destroy']);
    Route::delete('/communications/{id}', [App\Http\Controllers\CommunicationController::class, 'destroy']);
});

Route::middleware(['auth', 'role:admin,accountant'])->group(function () {
    Route::get('/billing', [BillingController::class, 'index']);
    Route::get('/billing/create', [BillingController::class, 'create']);
    Route::get('/billing/{id}/send-email', [BillingController::class, 'sendInvoiceEmail']);
    Route::resource('billings', BillingController::class)->except(['create', 'show']);

    // Expense Tracking routes
    Route::get('/billing/expenses', [App\Http\Controllers\ExpenseController::class, 'index']);
    Route::get('/billing/expenses/create', [App\Http\Controllers\ExpenseController::class, 'create']);
    Route::post('/billing/expenses/store', [App\Http\Controllers\ExpenseController::class, 'store']);
    Route::get('/billing/expenses/edit/{id}', [App\Http\Controllers\ExpenseController::class, 'edit']);
    Route::post('/billing/expenses/update/{id}', [App\Http\Controllers\ExpenseController::class, 'update']);
    Route::get('/billing/expenses/delete/{id}', [App\Http\Controllers\ExpenseController::class, 'destroy']);
    Route::delete('/billing/expenses/{id}', [App\Http\Controllers\ExpenseController::class, 'destroy']);
});
/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard']);

    Route::get('/about', function () {
        return view('client.client-about');
    });

    Route::get('/properties', [ClientPortalController::class, 'properties']);

    Route::get('/property-details', [ClientPortalController::class, 'propertyDetails']);

    Route::get('/bookings', [ClientPortalController::class, 'bookings']);

    Route::get('/payments', [ClientPortalController::class, 'payments']);
    Route::get('/payments/{id}/download', [ClientPortalController::class, 'downloadInvoice']);
    Route::post('/request-visit', [ClientPortalController::class, 'requestVisit']);
    Route::post('/interested-property', [ClientPortalController::class, 'interestedProperty']);

    Route::get('/contact', function () {
        return view('client.client-contact');
    });

    Route::get('/privacy', function () {
        return view('client.client-privacy');
    });

    Route::get('/support', function () {
        return view('client.client-support');
    });

    Route::get('/updates', [ClientPortalController::class, 'updates']);
});

/*
|--------------------------------------------------------------------------
| STATIC PAGES
|--------------------------------------------------------------------------
*/
Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/support', function () {
    return view('support');
});
