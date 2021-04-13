<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balances;
use \App\Flash;
use \App\BalanceMessages;
use \App\Auth;

class Balance extends \Core\Controller
{
	public $summary;

	public function createAction()
    {
        Balances::displayFromSelectedPeriod();
    }

    public function browseAction()
    {	
	
	if (! Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
		
		$browse = new Balances($_POST);
		
		if ($browse->periodSelect == '4' && ($browse->beginDate == '' || $browse->endDate == '') )
		{
			Flash::addMessage('Nie wprowadzono zakresu dat', 'alert alert-warning');
			BalanceMessages::addMessage($browse->incomes, $browse->expenses, $browse->sumOfIncomes, $browse->sumOfExpenses, $browse->expensesToChart, $browse->periodSelect);
			View::renderTemplate('Balance/browse.html');
		}
		elseif ($browse->periodSelect == '4' && ($browse->beginDate > $browse->endDate) )
		{
			Flash::addMessage('Początkowa data musi być datą wcześniejszą od końcowej', 'alert alert-warning');
			BalanceMessages::addMessage($browse->incomes, $browse->expenses, $browse->sumOfIncomes, $browse->sumOfExpenses, $browse->expensesToChart, $browse->periodSelect);
			View::renderTemplate('Balance/browse.html');
		}
		else
		{
			$browse->displayDataFromSelectedPeriod();
			$browse->sumOfIncomes();
			$browse->sumOfExpenses();
			
			if ($browse->sumOfIncomes == "")
			{
				$browse->sumOfIncomes = "Brak przychodów w wybranym okresie";
			}
					
			if ($browse->sumOfExpenses == "")
			{
				$browse->sumOfExpenses = "Brak wydatków w wybranym okresie";
			}
			
			if ($browse->sumOfIncomes > $browse->sumOfExpenses)
			{
				$summary [] = [
				'body' => "Świetnie, wydajesz mniej niż zarabiasz w wybranym okresie!",
				'type' => "text-success" ];
			}
			elseif ($browse->sumOfIncomes < $browse->sumOfExpenses)
			{
				$summary [] = [
				'body' => "Uważaj, wydajesz więcej niż zarabiasz w wybranym okresie!",
				'type' => "text-danger" ];
			}
			else
			{
				$summary [] = [
				'body' => "Wydajesz tyle samo co zarabiasz w wybranym okresie.",
				'type' => "text-warning" ];
			}
				
			BalanceMessages::addMessage($browse->incomes, $browse->expenses, $browse->sumOfIncomes, $browse->sumOfExpenses, $browse->expensesToChart, $browse->periodSelect, $summary);
			View::renderTemplate('Balance/browse.html');
		}	
    }
}
