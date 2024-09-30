<?php
$lang['wallet'] = 'Portefeuille';
$lang['wallet_client_menu'] = 'Portefeuille';
$lang['wallet_staff_menu'] = 'Portefeuille';
$lang['wallet_transactions'] = 'Transactions';
$lang['wallet_transaction_mode'] = 'Mode';
$lang['wallet_amount'] = 'Montant';
$lang['wallet_transaction_tag'] = 'Étiquette';
$lang['wallet_total_credited'] = 'Financement total';
$lang['wallet_total_debited'] = 'Débit total';
$lang['wallet_balance'] = 'Balance';
$lang['wallet_total_balance'] = 'Solde des portefeuilles (estimation)';
$lang['wallet_add_fund'] = 'Ajouter un fonds';
$lang['wallet_invalid_funding_amount_min'] = 'Le montant ne peut être inférieur à %s %s';
$lang['wallet_invalid_funding_amount_range'] = 'Le montant doit être compris entre %s et %s %s';
$lang['wallet_funding_with_invoice_desc'] = 'Financement du solde du portefeuille';
$lang['wallet_withdrawal_with_invoice_desc'] = 'Retrait du portefeuille';
$lang['wallet_funding_confirm_notice'] = 'Une facture sera créée pour le financement du portefeuille. Cliquez sur Ok pour continuer';
$lang['wallet_revalidate_funding'] = 'Revalider';
$lang['wallet_min_funding_amount'] = 'Montant minimum de financement/retrait';
$lang['wallet_max_funding_amount'] = 'Montant maximum de financement/retrait';
$lang['wallet_max_funding_amount_hint'] = 'Régler à -1 pour un nombre illimité';
$lang['wallet_funding_allowed_payment_modes'] = 'Méthodes de paiement autorisées pour le financement du portefeuille';
$lang['wallet_no_payments_found'] = 'Aucun paiement n\'a encore été trouvé pour la transaction';
$lang['wallet_error_crediting_balance'] = 'Erreur de crédit du portefeuille';
$lang['wallet_enable_overdue_invoice_auto_payment'] = 'Activer le paiement automatique des factures en retard';
$lang['wallet_enable_overdue_invoice_auto_payment_hint'] = 'L\'activation de cette option déclenche des tentatives de paiement automatiques pour les factures en retard de paiement en utilisant le solde du portefeuille du contact principal.';
$lang['wallet_dashboard_menu'] = 'Vue d\'ensemble';
$lang['wallet_tag_funding'] = 'financement';
$lang['wallet_tag_invoice payment'] = 'paiement des factures';
$lang['wallet_tag_reversal'] = 'renversement';
$lang['wallet_tag_withdrawal'] = 'se retirer';
$lang['wallet_tag_cancelled_withdrawal'] = 'retrait annulé';
$lang['wallet_error_adding_funding_log'] = 'Erreur lors de l\'ajout d\'un journal de financement';
$lang['wallet_error_withdrawing_log'] = 'Erreur lors de l\'ajout du journal des retraits';
$lang['wallet_error_debiting_balance'] = 'Erreur de chargement du portefeuille';
$lang['wallet_payment_reversal'] = 'Contre-passation pour paiement';
$lang['wallet_reverse'] = 'Inverser';
$lang['wallet_enabled_reversal'] = 'Autoriser l\'option d\'annulation des paiements';
$lang['wallet_enabled_reversal_hint'] = 'Si oui, le paiement est annulé lorsque le paiement est supprimé, et un bouton d\'annulation est également disponible près de chaque transaction. Les transactions financées par le portefeuille ne sont toutefois pas annulées.';
$lang['wallet_updated_at'] = 'Date de mise à jour';
$lang['wallet_welcome'] = 'Bienvenue %s !';
$lang['wallet_fund'] = 'Fonds';
$lang['wallet_withdraw'] = 'Se retirer';
$lang['wallet_withdrawals'] = 'Retraits';
$lang['wallet_withdrawal_requests'] = 'Demandes de retrait';
$lang['wallet_pending_withdrawals'] = 'Vous avez %s demandes de retrait en attente';
$lang['wallet_withdraw_request'] = 'Demande de retrait';
$lang['wallet_withdrawal_methods'] = 'Méthodes de retrait';
$lang['wallet_withdrawal_note_required'] = 'Les détails de la méthode de retrait sont requis. Veuillez fournir vos coordonnées en fonction de la méthode choisie.';
$lang['wallet_withdrawal_methods_hint'] = 'Valeur séparée par des virgules (,) de la méthode de paiement du retrait, par exemple Paypal, Banque';
$lang['wallet_allow_withdraw'] = 'Autoriser le retrait';
$lang['wallet_withdraw_info_title'] = 'Se retirer';
$lang['wallet_withdraw_info_method'] = 'Méthode de retrait';
$lang['wallet_withdraw_info_details'] = 'Détails de la méthode de retrait';
$lang['wallet_withdraw_info_placeholder'] = 'Fournir toutes les informations nécessaires concernant la méthode de retrait choisie, c\'est-à-dire l\'email de Paypal, les coordonnées bancaires complètes avec le code SWIFT si vous utilisez une banque, etc.';
$lang['wallet_permission_transact'] = 'Transact';
$lang['wallet_gateway_settings'] = 'Paramètres de la passerelle du portefeuille';
$lang['wallet_gateway_admin_note'] = 'Lorsque cette option est activée, le client peut payer avec le solde de son portefeuille lorsqu\'il est connecté pendant l\'exécution de la facture.';
$lang['wallet_unsupported_currency'] = 'Monnaie non soutenue';
$lang['wallet_invoice_payment_note'] = 'Paiement de la facture %s';
$lang['wallet_insufficient_balance'] = 'Solde du portefeuille insuffisant';
$lang['wallet_error_charging_balance'] = 'Erreur de chargement du portefeuille';
$lang['wallet_gateway_invalid_amount'] = 'La facture est déjà payée';
$lang['wallet_gateway_invalid_payment_gateway'] = 'Une demande de financement du portefeuille ne peut pas être traitée par le portefeuille.';
$lang['wallet_withdraw_admin_note'] = 'Note';
$lang['wallet_optional'] = 'En option';
$lang['wallet_cancelled'] = 'Annulé';
$lang['wallet_approved'] = 'Approuvé';
$lang['wallet_allow_funding'] = 'Permettre aux clients de financer leur portefeuille';
$lang['wallet_withdrawal_not_allowed'] = 'Le retrait des fonds du portefeuille n\'est pas autorisé.';
$lang['wallet_funding_not_allowed'] = 'Le financement du portefeuille n\'est pas autorisé';
$lang['wallet_initial_credit_amount'] = 'Montant initial du crédit du portefeuille libre';
$lang['wallet_initial_credit_amount_help'] = 'Indiquez le montant gratuit à créditer dans les portefeuilles nouvellement créés, c\'est-à-dire le bonus de bienvenue.';
$lang['wallet_initial_credit_amount_transaction_description'] = 'Compte de bienvenue ouverture de crédit gratuit !';
$lang['wallet_tag_system'] = 'système';
$lang['wallet_transcation_description'] = 'Description';
$lang['wallet_gateway_not_enabled'] = 'La passerelle du portefeuille doit être activée pour utiliser la fonction de retrait.';
$lang['wallet_reference_in_use'] = 'Référence déjà utilisée';
$lang['wallet_contact_not_found'] = 'Contact non trouvé';
$lang['wallet_withrdawal_approve'] = 'Approuver';
$lang['wallet_withrdawal_cancel'] = 'Annuler';