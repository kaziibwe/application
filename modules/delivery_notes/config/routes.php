<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['delivery_notes/admin/custom_fields/(:any)'] = 'delivery_note_custom_fields/$1';
$route['delivery_notes/admin/custom_fields/(:any)/(:any)'] = 'delivery_note_custom_fields/$1/$2';
$route['delivery_notes/admin/custom_fields/(:any)/(:any)/(:any)'] = 'delivery_note_custom_fields/$1/$2/$3';
