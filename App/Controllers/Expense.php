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
                
            ]);
        }
    }
	 
    public function checkLimitAction()
    {
	
		$input = json_decode(file_get_contents('php://input'), true);
		$data = Expenses::getCategoryData($input["category"]);
		$alreadySpent = Expenses::getThisMonthExpensesFromDB($input["category"]);
		$alreadySpentSum = $alreadySpent[0]["SUM(amount)"];
		
		if ($alreadySpentSum == "")
		{
			$alreadySpentSum = 0;
		}

		$balance = $input["value"] + $alreadySpentSum;
		$aboveLimit = ($balance - $data[0]['month_limit']);
		$aboveLimit = number_format((float) $aboveLimit , 2, '.', ''); 
		$isInCurrentMonth = Expenses::checkIfIsInCurrentMonth($input["date"]);
		
		if (($data[0]["month_limit_activated"] == 1) && $isInCurrentMonth)
		{
			if ($aboveLimit <= 0)
			{
				echo json_encode(
				'<div class="alert alert-success" role="alert">' . "Limit w kategorii " . $data[0]['name'] . ": ". $data[0]['month_limit'] . ". Dotychczas  wydano: " . $alreadySpentSum . ". Łącznie wydasz " . $balance . ". Mieścisz się w limicie. " . '</div>'
				);
			}
			else
			{
				echo json_encode(
				'<div class="alert alert-danger" role="alert">' . "Limit w kategorii " . $data[0]['name'] . ": ". $data[0]['month_limit'] . ". Dotychczas  wydano: " . $alreadySpentSum . ". Łącznie wydasz " . $balance . ". Przekraczasz limit o: " . $aboveLimit  . '</div>'
				);
			}
		}
		else
		{
			echo json_encode("");
		}	
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
