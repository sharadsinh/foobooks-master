<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    /**
	*
	*/
    public function books() {
        return $this->belongsToMany('\App\Book')->withTimestamps();;
    }

    
    public function getTagsForCheckboxes() {

        $tags = $this->orderBy('name','ASC')->get();

        $tagsForCheckboxes = [];

        foreach($tags as $tag) {
            $tagsForCheckboxes[$tag['id']] = $tag;
        }

        return $tagsForCheckboxes;

    }
}
