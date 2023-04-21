<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $url
 * @property    $contact_email_id
 * @property    $created_at
 * @property    $updated_at
 * Class ContactEmailAttachment
 * @package App\Models
 */

class ContactEmailAttachment extends Model
{
    protected $table = 'contact_email_attachment';
}