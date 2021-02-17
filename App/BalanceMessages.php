<?php

namespace App;

class BalanceMessages
{
    public static function addMessage($incomes, $expenses, $sumOfIncomes, $sumOfExpenses, $chartData, $dropDownSelect, $summary)
    {

        if (! isset($_SESSION['balance_messages'])) {
            $_SESSION['balance_messages'] = [];
        }

        $_SESSION['balance_messages'][] = [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'sumOfIncomes' => $sumOfIncomes,
            'sumOfExpenses' => $sumOfExpenses,
            'chartData' => $chartData,
            'dropDownSelect' => $dropDownSelect,
            'summary' => $summary
        ];
    }
	
    public static function getMessages()
    {
        if (isset($_SESSION['balance_messages'])) {
            $messages = $_SESSION['balance_messages'];
            unset($_SESSION['balance_messages']);

            return $messages;
        }
    }
}
