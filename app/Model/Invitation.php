<?php

namespace App\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Invitation extends Model
{
	protected $fillable = [
		'email', 'invitation_token', 'registered_at',
	];


	public function generateInvitationToken() {
		$this->invitation_token = substr(md5(rand(0, 9) . $this->email . time()), 0, 32);
	}
}
