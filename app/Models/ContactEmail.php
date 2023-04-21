<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property    $id
 * @property    $email
 * @property    $first_name
 * @property    $last_name
 * @property    $subject
 * @property    $description
 * @property    $created_at
 * @property    $updated_at
 * Class ContactEmail
 * @package App\Models
 */

class ContactEmail extends Model
{
    protected $table = 'contact_email';

}