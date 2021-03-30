<?php

namespace App\Controllers;
use \App\Models\User;

use \Core\View;
use \App\Models\Incomes;
use \App\Flash;
use \App\Auth;

use \App\Models\Setting;

class Settings extends \Core\Controller
{ 
    public function optionsAction()
    {
        View::renderTemplate('Settings/options.html');
    }
	
	public function changenameAction()
    {
        View::renderTemplate('Settings/changename.html');
    }
	
	public function updateNameAction()
    {
		$user = new User($_POST);
		
		if ($user->updateName()) {
			
			Flash::addMessage('Pomyślnie zmieniono imię', 'alert alert-success');
			View::renderTemplate('settings/options.html');

        } else {
			
			foreach ($user->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}

            View::renderTemplate('settings/changename.html', [
                'user' => $user
            ]);					
        }
		
        
		//View::renderTemplate('Settings/options.html');
    }
	
	public function changemailAction()
    {
        View::renderTemplate('Settings/changemail.html');
    }
	
	public function updateEmailAction()
    {
		$user = new User($_POST);
		
		if ($user->updateEmail()) {
			
			Flash::addMessage('Pomyślnie zmieniono email', 'alert alert-success');
			View::renderTemplate('settings/options.html');

        } else {
			
			foreach ($user->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}

            View::renderTemplate('settings/changemail.html', [
                'user' => $user
            ]);					
        }
		
        
		//View::renderTemplate('Settings/options.html');
    }
	
	public function changepasswordAction()
    {
        View::renderTemplate('Settings/changepassword.html');
    }
	
	public function updatePasswordAction()
    {
		$user = new User($_POST);
		$userData = User::findByID($_SESSION['user_id']);
		
		if(User::authenticate($userData->email, $user->oldPassword))
		{
			if ($user->updatePassword()) {
			
			Flash::addMessage('Pomyślnie zmieniono hasło', 'alert alert-success');
			View::renderTemplate('settings/options.html');

        } else {
			
			foreach ($user->errors as &$value) {
			Flash::addMessage($value, 'alert alert-danger');
			}

            View::renderTemplate('settings/changepassword.html', [
                'user' => $user
            ]);					
        }
		}
		else
		{
			Flash::addMessage('Wprowadzono niepoprawne obecne hasło', 'alert alert-warning');
			View::renderTemplate('settings/changepassword.html');
		}
		
		
		
        
		//View::renderTemplate('Settings/options.html');
    }
	
	
	public function paymentmethodAction()
    {
        View::renderTemplate('Settings/paymentmethod.html');
    }
	
	public function incomecategoryAction()
    {
        View::renderTemplate('Settings/incomecategory.html');
    }

	public function expensecategoryAction()
    {
        View::renderTemplate('Settings/expensecategory.html');
    }
	
	public function addNewPaymentMethodAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfPaymentMethodAlreadyExists())
		{
			Flash::addMessage('Metoda płatności o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/paymentmethod.html');
		}
		else
		{
			$setup->addPaymentMethod();
			Flash::addMessage('Pomyślnie dodano metodę płatności', 'alert alert-success');
			View::renderTemplate('Settings/paymentmethod.html');
		}
		
    }
	
	public function deletePaymentMethodAction()
    {
        $setup = new Setting($_POST);
		
		$setup->deletePaymentMethod();
		View::renderTemplate('Settings/paymentmethod.html');
    }
	
	public function editPaymentMethodAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfPaymentMethodAlreadyExists())
		{
			Flash::addMessage('Metoda płatności o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/paymentmethod.html');
		}
		else
		{
			$setup->updatePaymentMethod();
			Flash::addMessage('Pomyślnie edytowano metodę płatności', 'alert alert-success');
			View::renderTemplate('Settings/paymentmethod.html');
		}
		
    }









public function addNewIncomeCategoryAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfIncomeCategoryAlreadyExists())
		{
			Flash::addMessage('Kategoria przychodu o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/incomecategory.html');
		}
		else
		{
			$setup->addIncomeCategory();
			Flash::addMessage('Pomyślnie dodano kategorię przychodu', 'alert alert-success');
			View::renderTemplate('Settings/incomecategory.html');
		}
		
    }
	
	public function deleteIncomeCategoryAction()
    {
        $setup = new Setting($_POST);
		
		$setup->deleteIncomeCategory();
		View::renderTemplate('Settings/incomecategory.html');
    }
	
	public function editIncomeCategoryAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfIncomeCategoryAlreadyExists())
		{
			Flash::addMessage('Kategoria przychodu o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/incomecategory.html');
		}
		else
		{
			$setup->updateIncomeCategory();
			Flash::addMessage('Pomyślnie edytowano kategorię przychodu', 'alert alert-success');
			View::renderTemplate('Settings/incomecategory.html');
		}
		
    }
	
	
	
	
	
	
	
	
	public function addNewExpenseCategoryAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfExpenseCategoryAlreadyExists())
		{
			Flash::addMessage('Kategoria wydatku o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/expensecategory.html');
		}
		else
		{
			$setup->addExpenseCategory();
			Flash::addMessage('Pomyślnie dodano kategorię wydatku', 'alert alert-success');
			View::renderTemplate('Settings/expensecategory.html');
		}
		
    }
	
	public function deleteExpenseCategoryAction()
    {
        $setup = new Setting($_POST);
		
		$setup->deleteExpenseCategory();
		View::renderTemplate('Settings/expensecategory.html');
    }
	
	public function editExpenseCategoryAction()
    {
        $setup = new Setting($_POST);
		
		if ($setup->checkIfExpenseCategoryAlreadyExists())
		{
			Flash::addMessage('Kategoria wydatku o takiej nazwie już istnieje', 'alert alert-warning');
			View::renderTemplate('settings/expensecategory.html');
		}
		else
		{
			$setup->updateExpenseCategory();
			
			Flash::addMessage('Pomyślnie edytowano kategorię wydatku', 'alert alert-success');
			View::renderTemplate('Settings/expensecategory.html');
		}
		
    }


}
