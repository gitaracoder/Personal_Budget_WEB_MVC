<?php

namespace Core;
use \App\Models\Balances;
/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
		
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
			$twig->addGlobal('flash_messages', \App\Flash::getMessages());
			$twig->addGlobal('incomeCategoriesAssignedToUser', \App\Models\Incomes::getIncomeCategoriesAssigned());
			$twig->addGlobal('expenseCategoriesAssignedToUser', \App\Models\Expenses::getExpenseCategoriesAssigned());
			$twig->addGlobal('paymentMethodsAssignedToUser', \App\Models\Expenses::getPaymentMethodsAssigned());
			$twig->addGlobal('messages_in_balance', \App\BalanceMessages::getMessages());
			//$twig->addGlobal('expenseLimitStatus', \App\Controllers\Expense::checkLimitAction());
        }

        echo $twig->render($template, $args);
    }
}
