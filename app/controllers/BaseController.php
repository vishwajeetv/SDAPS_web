<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}


	public function response($status, $message, $body)
	{
		return Response::json
		([
			'header' => array(
				'status' => $status,
				'message' => $message
			),
			'body' =>
				$body
		],
			200
		);
	}

}
