<?php

namespace SaurabhDhariwal\Comments\Models;

use Model;
use RainLab\Blog\Models\Post;

/**
 * Model
 */
class Comments extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     *
     */
    const STATUS = ['1' => 'Approved', '2' => 'Pending', '3' => 'Spam'];
    /**
     *
     */
    const STATUS_APPROVED = 1;

    /*
     * Validation
     */
    /**
     * @var array
     */
    public $rules = [];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
//    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'saurabhdhariwal_comments_posts';


    /**
     * @var array
     */
    public $belongsTo = [
       'user' => ['RainLab\User\Models\User']
   ];

    /**
     * @param null $keyValue
     * @return array
     */
    public function getStatusOptions($keyValue = null)
    {
        return self::STATUS;
    }


    /**
     * @return mixed
     */
    public function getStatusAdminAttribute()
    {
        return self::STATUS[$this->status];
    }

    /**
     * @return string
     */
    public function getAvatarAttribute()
    {
        return "<img src='http://www.gravatar.com/avatar/" . md5($this->author.$this->id) . "/?d=wavatar&r=pg'/>";
    }

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        if ($this->author != "") {
            return $this->author;
        } elseif ($this->user) {
            return $this->user->name;
        }
    }

    public function title()
    {
        $slug = str_replace('post/', '', $this->url);
        return Post::whereSlug($slug)->first()->title;
    }

    public function getRecentComments($number)
    {
        return Comments::orderBy('updated_at', 'desc')->take($number)->get()->map(function ($el) {
            return [
            'id' => $el->id,
            'author' => $el->author,
            'date' => $el->updated_at->toDateString(),
            'url' => $el->url,
            'title' => $el->title()];
        })->toArray();
    }
}
