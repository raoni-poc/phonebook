<?php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'agenda_telefonica',
        'user' => 'root',
        'password' => '5AShKf7osHyj',
    ],
    'routes' => [
        'home' => [
            "route"=>'/',
            "controller"=>"indexController",
            "action"=>"index"
        ],
        'phonebook.create' => [
            "route"=>'/phonebook/create',
            "controller"=>"PhonebookController",
            "action"=>"create"
        ],
        'phonebook.delete' => [
            "route"=>'/phonebook/delete',
            "controller"=>"PhonebookController",
            "action"=>"delete"
        ],
        'phonebook.store' => [
            "route"=>'/phonebook/store',
            "controller"=>"PhonebookController",
            "action"=>"store"
        ],
        'phonebook.show' => [
            "route"=>'/phonebook/show',
            "controller"=>"PhonebookController",
            "action"=>"show"
        ],
        'phonebook.edit' => [
            "route"=>'/phonebook/edit',
            "controller"=>"PhonebookController",
            "action"=>"edit"
        ],
        'phonebook.update' => [
            "route"=>'/phonebook/update',
            "controller"=>"PhonebookController",
            "action"=>"update"
        ],
        'phonebook' => [
            "route"=>'/phonebook',
            "controller"=>"PhonebookController",
            "action"=>"index"
        ],
        'phonebook.json' => [
            "route"=>'/phonebook/json',
            "controller"=>"PhonebookController",
            "action"=>"json"
        ],
    ],
];
