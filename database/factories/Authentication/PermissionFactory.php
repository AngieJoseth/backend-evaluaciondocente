<?php

namespace Database\Factories\Authentication;

use App\Models\Authentication\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{

    protected $model = Permission::class;

    public function definition()
    {
        return [
            'actions' => $this->faker
                ->randomElements(
                    $array = array(
                        Permission::CREATE_ACTION,
                        Permission::UPDATE_ACTION,
                        Permission::DELETE_ACTION,
                        Permission::SELECT_ACTION),
                    $count = random_int(1, 4)),
            'state_id' => 1
        ];
    }
}
