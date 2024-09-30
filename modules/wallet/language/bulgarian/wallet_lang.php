<?php
$lang['wallet'] = 'Портфейл';
$lang['wallet_client_menu'] = 'Портфейл';
$lang['wallet_staff_menu'] = 'Портфейл';
$lang['wallet_transactions'] = 'Транзакции';
$lang['wallet_transaction_mode'] = 'Режим';
$lang['wallet_amount'] = 'Сума';
$lang['wallet_transaction_tag'] = 'Етикет';
$lang['wallet_total_credited'] = 'Общо финансиране';
$lang['wallet_total_debited'] = 'Общ дебит';
$lang['wallet_balance'] = 'Баланс';
$lang['wallet_total_balance'] = 'Баланс в портфейлите (оценка)';
$lang['wallet_add_fund'] = 'Добавяне на фонд';
$lang['wallet_invalid_funding_amount_min'] = 'Сумата не може да бъде по-малка от %s %s';
$lang['wallet_invalid_funding_amount_range'] = 'Сумата трябва да е между %s и %s %s';
$lang['wallet_funding_with_invoice_desc'] = 'Финансиране на баланса на портфейла';
$lang['wallet_withdrawal_with_invoice_desc'] = 'Изтегляне на средства от портфейла';
$lang['wallet_funding_confirm_notice'] = 'Ще бъде създадена фактура за финансиране на портфейла. Щракнете върху ОК, за да продължите';
$lang['wallet_revalidate_funding'] = 'Потвърждаване на';
$lang['wallet_min_funding_amount'] = 'Минимална сума за финансиране/теглене';
$lang['wallet_max_funding_amount'] = 'Максимална сума за финансиране/теглене';
$lang['wallet_max_funding_amount_hint'] = 'Задайте -1 за неограничен брой';
$lang['wallet_funding_allowed_payment_modes'] = 'Разрешени методи на плащане за финансиране на портфейла';
$lang['wallet_no_payments_found'] = 'Все още няма намерено плащане за транзакцията';
$lang['wallet_error_crediting_balance'] = 'Грешка при кредитирането на портфейла';
$lang['wallet_enable_overdue_invoice_auto_payment'] = 'Активиране на автоматично плащане за просрочени фактури';
$lang['wallet_enable_overdue_invoice_auto_payment_hint'] = 'Активирането на тази опция инициира автоматични опити за плащане на фактура(и) с просрочени плащания, като се използва балансът в портфейла на основния контакт.';
$lang['wallet_dashboard_menu'] = 'Преглед';
$lang['wallet_tag_funding'] = 'финансиране';
$lang['wallet_tag_invoice payment'] = 'плащане по фактура';
$lang['wallet_tag_reversal'] = 'обръщане';
$lang['wallet_tag_withdrawal'] = 'оттегляне';
$lang['wallet_tag_cancelled_withdrawal'] = 'отменено оттегляне';
$lang['wallet_error_adding_funding_log'] = 'Грешка при добавяне на дневник за финансиране';
$lang['wallet_error_withdrawing_log'] = 'Грешка при добавяне на дневник за изтегляне';
$lang['wallet_error_debiting_balance'] = 'Грешка при зареждането на портфейла';
$lang['wallet_payment_reversal'] = 'Сторниране за плащане';
$lang['wallet_reverse'] = 'Обратен';
$lang['wallet_enabled_reversal'] = 'Разрешаване на опция за сторниране на плащане';
$lang['wallet_enabled_reversal_hint'] = 'Ако отговорът е \\\\\\\\\\\\\"да\\\\\\\\\\\\\", плащането се сторнира при изтриване на плащането, а до всяка транзакция е наличен бутон за сторниране. Транзакциите за финансиране от портфейла обаче няма да бъдат анулирани.';
$lang['wallet_updated_at'] = 'Дата на актуализация';
$lang['wallet_welcome'] = 'Добре дошли %s!';
$lang['wallet_fund'] = 'Фонд';
$lang['wallet_withdraw'] = 'Изтегляне на';
$lang['wallet_withdrawals'] = 'Изтеглени суми';
$lang['wallet_withdrawal_requests'] = 'Искания за оттегляне';
$lang['wallet_pending_withdrawals'] = 'Имате %s чакащи заявки за теглене';
$lang['wallet_withdraw_request'] = 'Искане за оттегляне';
$lang['wallet_withdrawal_methods'] = 'Методи за оттегляне';
$lang['wallet_withdrawal_note_required'] = 'Изисква се информация за метода на теглене. Моля, предоставете данните си в зависимост от избрания метод.';
$lang['wallet_withdrawal_methods_hint'] = 'Разделена със запетая (,) стойност на метода на плащане за изтегляне, т.е. Paypal, Bank';
$lang['wallet_allow_withdraw'] = 'Разрешаване на изтеглянето';
$lang['wallet_withdraw_info_title'] = 'Изтегляне на';
$lang['wallet_withdraw_info_method'] = 'Метод на теглене';
$lang['wallet_withdraw_info_details'] = 'Данни за метода на теглене';
$lang['wallet_withdraw_info_placeholder'] = 'Предоставете цялата необходима информация за избрания от вас метод за теглене, т.е. имейл на Paypal, пълна банкова информация със SWIFT код, ако използвате банка, и т.н.';
$lang['wallet_permission_transact'] = 'Транзакция';
$lang['wallet_gateway_settings'] = 'Настройки на портала за портфейли';
$lang['wallet_gateway_admin_note'] = 'Когато е активирана, клиентът може да плати с баланса на портфейла си, когато влезе в системата по време на изпълнението на фактурата.';
$lang['wallet_unsupported_currency'] = 'Неподдържана валута';
$lang['wallet_invoice_payment_note'] = 'Плащане за фактура %s';
$lang['wallet_insufficient_balance'] = 'Недостатъчен баланс на портфейла';
$lang['wallet_error_charging_balance'] = 'Грешка при зареждането на портфейла';
$lang['wallet_gateway_invalid_amount'] = 'Фактурата вече е платена';
$lang['wallet_gateway_invalid_payment_gateway'] = 'Заявка за финансиране на портфейл не може да бъде обработена от портфейла';
$lang['wallet_withdraw_admin_note'] = 'Забележка';
$lang['wallet_optional'] = 'По избор';
$lang['wallet_cancelled'] = 'Отменен';
$lang['wallet_approved'] = 'Одобрен';
$lang['wallet_allow_funding'] = 'Позволете на клиентите да финансират портфейл';
$lang['wallet_withdrawal_not_allowed'] = 'Не се разрешава теглене на средства от портфейла.';
$lang['wallet_funding_not_allowed'] = 'Финансирането на портфейл не е разрешено';
$lang['wallet_initial_credit_amount'] = 'Първоначална сума на кредита за безплатен портфейл';
$lang['wallet_initial_credit_amount_help'] = 'Посочете безплатната сума, която да бъде кредитирана в новосъздадените портфейли. т.е. бонус за добре дошли';
$lang['wallet_initial_credit_amount_transaction_description'] = 'Добре дошли за откриване на сметка безплатен кредит!';
$lang['wallet_tag_system'] = 'система';
$lang['wallet_transcation_description'] = 'Описание';
$lang['wallet_gateway_not_enabled'] = 'За да използвате функцията за теглене, трябва да е активиран портфейлът.';
$lang['wallet_reference_in_use'] = 'Вече използвана референция';
$lang['wallet_contact_not_found'] = 'Контактът не е намерен';
$lang['wallet_withrdawal_approve'] = 'Одобряване на';
$lang['wallet_withrdawal_cancel'] = 'Отмяна на';
