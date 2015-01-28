<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	public function postCreate()
	{
		$user = new User;
		$user->name     = Input::get('name');
		$user->username = Input::get('username');
		$user->email    = Input::get('email');
		$user->password = Hash::make(Input::get('password'));

		$user->save();

		return $this->response("success","user created",$user);
	}

	public function postSignIn()
	{
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|alphaNum|min:3'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return $this->response("failed","fields are invalid",null);
		} else {
				$userdata = array(
					'email'     => Input::get('email'),
					'password'  => Input::get('password')
				);

			if (Auth::attempt($userdata)) {
				return $this->response("success","logged in",null);
			} else {
				return $this->response("failed","wrong credentials",null);
			}

		}
	}

}
