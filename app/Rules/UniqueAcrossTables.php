<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueAcrossTables implements Rule
{

    private $table1;
    private $table2;
    private $column;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table1, $table2, $column)
    {
        $this->table1 = $table1;
        $this->table2 = $table2;
        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $count1 = DB::table($this->table1)
            ->where($this->column, $value)
            ->count();

        $count2 = DB::table($this->table2)
            ->where($this->column, $value)
            ->count();

        return ($count1 + $count2) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not unique in both tables.';
    }
}
