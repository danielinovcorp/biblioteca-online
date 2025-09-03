<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EncomendaPagaMail;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            if ($secret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
            } else {
                // Sem verificação de assinatura (dev only). Não recomendado em produção.
                $event = json_decode($payload);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('invalid', 400);
        }

        $type = $event->type ?? $event->type ?? null;

        try {
            switch ($type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $sessionId = $session->id ?? null;
                    $paymentIntent = $session->payment_intent ?? null;

                    if ($sessionId) {
                        $encomenda = Encomenda::where('stripe_session_id', $sessionId)->first();
                        if ($encomenda) {
                            $encomenda->estado = 'paga';
                            if ($paymentIntent) $encomenda->stripe_payment_intent = $paymentIntent;
                            $encomenda->save();
							Mail::to($encomenda->user->email)->send(new EncomendaPagaMail($encomenda));
                            // TODOS: enviar e-mail de confirmação ao cliente
                        }
                    }
                    break;

                case 'payment_intent.succeeded':
                    $pi = $event->data->object;
                    $piId = $pi->id;
                    // Opcional: cruzar por payment_intent
                    $encomenda = Encomenda::where('stripe_payment_intent', $piId)->first();
                    if ($encomenda) {
                        $encomenda->estado = 'paga';
                        $encomenda->save();
						Mail::to($encomenda->user->email)->send(new EncomendaPagaMail($encomenda));
                    }
                    break;

                // podes tratar outros eventos se quiseres
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook handler failed', ['error' => $e->getMessage()]);
            return response('error', 500);
        }

        return response('ok', 200);
    }
}
