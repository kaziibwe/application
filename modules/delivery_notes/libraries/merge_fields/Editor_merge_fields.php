<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Editor_merge_fields extends App_merge_fields
{
    public function build()
    {

        $available = ['delivery_note'];

        return [
            [
                'name'      => 'Editor Firstname',
                'key'       => '{editor_firstname}',
                'available' => $available,
            ],
            [
                'name'      => 'Editor Lastname',
                'key'       => '{editor_lastname}',
                'available' => $available,
            ],
            [
                'name'      => 'Editor Email',
                'key'       => '{editor_email}',
                'available' => $available,
            ],
        ];
    }

    /**
     * Merge field for staff members
     * @param  object $editor
     * @param  string $password password is used only when sending welcome email, 1 time
     * @return array
     */
    public function format($editor)
    {
        $fields = [];
        $fields['{editor_firstname}']   = $editor->firstname ?? '';
        $fields['{editor_lastname}']    = $editor->lastname ?? '';
        $fields['{editor_email}']       = $editor->email ?? '';

        // backward compact
        $fields['{editor_staff_firstname}']   = $editor->firstname ?? '';
        $fields['{editor_staff_lastname}']    = $editor->lastname ?? '';
        $fields['{editor_staff_email}']       = $editor->email ?? '';

        return hooks()->apply_filters('editor_merge_fields', $fields, [
            'editor' => $editor,
        ]);
    }
}
