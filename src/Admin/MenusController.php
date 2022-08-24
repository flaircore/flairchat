<?php

namespace Flair\Chat\Admin;

class MenusController {

    public function __constructor() {
        die('WHAT');
        add_action('admin_menu', array($this, 'add_menus'));
    }

    public function initialize_menus(): array
    {
        return [
            'flair-chat' => [
                'page_title' => 'Flair Chat',
                'menu_title' => 'Flair Chat',
                'capability' => 'administrator',
                'function' => array($this, 'menu_view'),
                'icon_url' => '',
                'priority' => 90,
            ]
        ];
    }

    public function menu_view(): string
    {
        return '<h1>Page title!</h1>';
    }

    public function add_menus() {
        dump('HERE NOW');
        foreach ($this->initialize_menus() as $menu_slug => $menu ) {
            add_menu_page(
                $menu['page_title'],
                $menu['menu_title'],
                $menu['capability'],
                $menu_slug,
                $menu['function'],
                $menu['icon_url'],
                $menu['priority']
            );
        }
    }

}