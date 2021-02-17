<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expenses;
use \App\Flash;
use \App\Auth;

class Expense extends \Core\Controller
{
 
	public function createAction()
    {
		if (! Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
		
        $expense = new Expenses($_POST);

        if ($expense->save()) {
       
			Flash::addMessage('Pomyślnie dodano wydatek', 'alert alert-success');
			View::renderTemplate('Home/index.html');

        } else {
			
			foreach ($expense->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}
			
            View::renderTemplate('Expense/new.html', [
                //'income' => $income
            ]);
			//var_dump($income->errors);
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
		
        View::renderTemplate('Expense/new.html');
    }

    public function successAction()
    {
        echo "Pomyślnie dodano wydatek";
    }
}
