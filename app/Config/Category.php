<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Category extends BaseConfig
{
    public array $category_type = [
        1 => 'Service',
        2 => 'Expense',
        3 => 'In-patient',
        4 => 'Operation ward'
    ];

    public array $yes_no_option = [
        0 => 'No',
        1 => 'Yes'
    ];
}
