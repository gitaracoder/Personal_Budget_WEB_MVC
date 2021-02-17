<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Incomes;
use \App\Models\Expenses;
use \App\Flash;

class Signup extends \Core\Controller
{

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {
			
			Incomes::copyDefaultCategories($user->chceckNewUserID());
			Expenses::copyDefaultCategories($user->chceckNewUserID());
			Expenses::copyDefaultPaymentMethods($user->chceckNewUserID());
			Flash::addMessage('Pomyślnie założonio konto, możesz sie teraz zalogować', 'alert alert-success');
			View::renderTemplate('Login/new.html');

        } else {
			
			foreach ($user->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);					
        }
    }

    public function successAction()
    {
        View::renderTemplate('Home/success.html');
    }
}
