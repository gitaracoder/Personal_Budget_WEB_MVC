<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Flash;
use \App\Auth;

class Income extends \Core\Controller
{
	 
	public function createAction()
    {
		if (! Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
		
        $income = new Incomes($_POST);

        if ($income->save()) {

			Flash::addMessage('Pomyślnie dodano wydatek', 'alert alert-success');
			View::renderTemplate('Home/index.html');

        } else {
			
			foreach ($income->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}

            View::renderTemplate('Income/new.html', [
                //'income' => $income
            ]);
        }
    }
	 
    public function indexAction()
    {
        //View::renderTemplate('Income/new.html');
    }

    public function newAction()
    {
		if (! Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
        View::renderTemplate('Income/new.html');
    }

    public function successAction()
    {
        echo "Pomyślnie dodano wydatek";
    }
}
