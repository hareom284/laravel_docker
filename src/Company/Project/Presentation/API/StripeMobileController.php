<?php

namespace Src\Company\Project\Presentation\API;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Stripe\PaymentIntent;

class StripeMobileController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        try {

            $companyName = env('COMPANY_FOLDER_NAME');

            if($companyName == 'Praxis') {
                $successUrl = 'http://localhost:8100/salesperson/account-tab';
                $cancelUrl = 'http://localhost:8100/salesperson/account-tab';
            } else {
                $successUrl = 'http://localhost:8100/salesperson/account-tab';
                $cancelUrl = 'http://localhost:8100/salesperson/account-tab';
            }

            // Stripe::setApiKey(env('STRIPE_SECRET'));

            // $paymentIntent = PaymentIntent::create([
            //     'amount' => 1000, // Amount in cents (e.g., 1000 = 10 SGD)
            //     'currency' => 'sgd',
            //     'payment_method_types' => ['card', 'paynow'],
            // ]);

            // return response()->success(['clientSecret' => $paymentIntent->client_secret], 'success', Response::HTTP_OK);

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::create([
                'payment_method_types' => ['card', 'paynow'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'sgd',
                        'product_data' => [
                            'name' => 'Your Product',
                        ],
                        'unit_amount' => 1000, // 10 SGD
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url($successUrl),
                'cancel_url' => url($cancelUrl),
            ]);

            return response()->success(['id' => $session->id], 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
        
    }
}
