<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class skills implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            $reward= \App\Models\Skill::create([
                'name' => ['en'=>$row[0],'ar'=>$row[0]],

            ]);

        }
    }
}
