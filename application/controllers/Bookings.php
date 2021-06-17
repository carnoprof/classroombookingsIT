<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;
use app\components\bookings\Grid;
use app\components\bookings\Agent;
use app\components\bookings\exceptions\AgentException;


class Bookings extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->lang->load('bookings');

		if ($this->userauth->is_level(TEACHER) && setting('maintenance_mode')) {
			$this->data['title'] = 'Bookings';
			$this->data['showtitle'] = '';
			$this->data['body'] = '';
			$this->render();
			$this->output->_display();
			exit();
		}

		$this->load->model('bookings_model');
		$this->load->helper('booking');
	}


	/**
	 * Main bookings page.
	 *
	 * Nearly everything handled through bookings Grid and Context components.
	 *
	 */
	public function index()
	{
		$context = new Context();

		$context->autofill([
			'base_uri' => $this->uri->segment(1),
		]);

		$grid = new Grid($context);

		$message = $this->session->flashdata('bookings');

		$this->data['title'] = 'Bookings';
		$this->data['showtitle'] = '';
		$this->data['body'] = $message . $grid->render();

		$arr = $context->toArray();
		$json = json_encode($arr, JSON_PRETTY_PRINT);
		// $this->data['body'] .= "<pre>{$json}</pre>";

		return $this->render();
	}


	/**
	 * View details for single booking.
	 *
	 * This is designed to be shown in a sidebar panel.
	 *
	 */
	public function view($booking_id)
	{
		$include = [
			'repeat',
			'session',
			'period',
			'week',
			'room',
			'user',
			'department',
			'repeat',
		];

		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		if ($booking) {
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/view', $this->data, TRUE);
		} else {
			$body = msgbox('error', 'Could not find requested booking details.');
		}

		$this->data['title'] = 'Booking details';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view" style="min-width: 320px">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * View all bookings in series.
	 * This is accessed from viewing details of one booking in a series.
	 *
	 */
	public function view_series($booking_id)
	{

		// Get booking to highlight it

		$include = [
			'period',
		];
		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;

		if ($booking) {
			$this->data['all_bookings'] = $this->bookings_model->find_by_repeat($booking->repeat_id);
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/view_series', $this->data, TRUE);
		} else {
			$body = msgbox('error', 'Could not find requested booking details.');
		}

		$this->data['title'] = 'Bookings in series';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view" style="min-width: 320px">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * Handle creation of a new booking.
	 *
	 * 'Type' should be supplied as the first URI param, with other relevant data coming in via query string.
	 *
	 * @param string $type		Type of booking [single|multi]
	 *
	 */
	public function create($type)
	{
		$this->load->library('table');

		$this->data['title'] = 'Create booking';
		$this->data['view'] = '';

		if ($this->input->get('params')) {
			$_SESSION['return_uri'] = 'bookings?' . $this->input->get('params');
		}

		try {

			$agent = Agent::create($type);
			// $agent->set_return_uri('bookings?' . $this->input->get('params'));
			$agent->load();

			if ($agent->process() === TRUE) {

				$this->session->set_flashdata('bookings', msgbox('info', $agent->message));

				$uri = isset($_SESSION['return_uri'])
					? $_SESSION['return_uri']
					: 'bookings';

				unset($_SESSION['return_uri']);
				redirect($uri);
				return;
			}

			$this->data['view'] = $agent->render();

		} catch (AgentException $e) {
			$this->data['view'] = msgbox('error', $e->getMessage());
		}

		// Render the main view
		$this->data['body'] = $this->load->view('bookings/create', $this->data, TRUE);

		return $this->render();
	}


/*

	private function create_single()
	{
		$period_id = $this->input->post_get('period');
		$room_id = $this->input->post_get('room');
		$date = $this->input->post_get('date');

		$period = $this->periods_model->Get($period_id);
		if ( ! $period) {
			$this->data['view'] = msgbox('error', 'Requested period could not be found.');
			return;
		}

		$room = $this->rooms_model->get_bookable_rooms($this->userauth->user->user_id, $room_id);
		if ( ! $room) {
			$this->data['view'] = msgbox('error', 'Requested room could not be found or is not bookable.');
			return;
		}

		$date_info = $this->dates_model->get_by_date($date);
		if ( ! $date_info) {
			$this->data['view'] = msgbox('error', 'Requested date is not recognised or is not bookable.');
			return;
		}

		$this->data['period'] = $period;
		$this->data['room'] = $room;
		$this->data['date_info'] = $date_info;
		$this->data['datetime'] = datetime_from_string($date_info->date);
		$this->data['params'] = $this->input->get('params');

		if ($this->input->post('action')) {
			$this->create_single_save();
		}

		$this->data['title'] = 'Create Booking';
		$this->data['view'] = $this->load->view('bookings/create/single', $this->data, TRUE);
	}*/

/*
	private function create_single_save()
	{
		$rules = [
			['field' => 'date', 'label' => 'Date', 'rules' => 'required|valid_date'],
			['field' => 'period', 'label' => 'Period', 'rules' => 'required|integer'],
			['field' => 'room', 'label' => 'Room', 'rules' => 'required|integer'],
			['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'],
		];

		$this->load->library('form_validation');
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == FALSE) {
			return;
		}
	}
*/

	/**
	 * Handle cancellation of existing booking.
	 *
	 * On viewing, shows different content depending on booking type (single / recurring).
	 * For recurring bookings, options will be presented for selected instance, all future instances, or all instances.
	 * For single bookings, just a confirmation.
	 *
	 * On form submission, the requested action is carried out.
	 *
	 */
	public function cancel($booking_id)
	{
		if ($this->input->get('params')) {
			$return_uri = 'bookings?' . $this->input->get('params');
			$_SESSION['return_uri'] = $return_uri;
			$this->data['return_uri'] = $return_uri;
		}

		$booking = $this->bookings_model->include(['room'])->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		switch (TRUE) {

			case ($booking === FALSE):
				$body = msgbox('error', 'Could not find requested booking details.');
				break;

			case (booking_cancelable($booking) === FALSE):
				$body = msgbox('error', 'Booking is not editable.');
				break;

			default:
				$body = $this->load->view('bookings/cancel', $this->data, TRUE);
		}

		if ($cancel_type = $this->input->post('cancel')) {

			$error = msgbox('error', 'There was an error cancelling the booking.');

			switch ($cancel_type) {

				case '1':
					$res = $this->bookings_model->cancel_single($booking_id);
					$success = msgbox('info', 'The booking has been cancelled.');
					break;

				case 'future':
					$res = $this->bookings_model->cancel_future($booking_id);
					$success = msgbox('info', 'The selected booking and all future occurrences in the series have been cancelled.');
					break;

				case 'all':
					$res = $this->bookings_model->cancel_all($booking_id);
					$success = msgbox('info', 'The whole recurring booking series has been cancelled.');
					break;

				default:
					$res = FALSE;
					$error = msgbox('error', 'Invalid cancellation type.');
			}

			$msg = ($res) ? $success : $error;
			$this->session->set_flashdata('bookings', $msg);

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			return redirect($uri);
		}

		$cls = 'bookings-cancel with-content';
		if ($booking->repeat_id) {
			$cls .= ' is-repeat';
		}

		$this->data['title'] = 'Cancel booking';
		$this->data['showtitle'] = '';
		$this->data['body'] = "<div class='{$cls}'>{$body}</div>";

		return $this->render();
	}


	/**
	 * An endpoint for Unpoly that returns an empty element.
	 *
	 * This allows certain responses that are loaded into the page to have a
	 * cancel or close action (pointing to /noop/) that will "remove" the
	 * previously-loaded content.
	 *
	 * An empty element with class or ID matching X-Up-Target will be returned.
	 *
	 */
	public function noop()
	{
		$target = $this->input->get_request_header('x-up-target');

		$attr = false;
		$out = '';

		switch (substr($target, 0, 1)) {
			case '.': $attr = 'class'; break;
			case '#': $attr = 'id'; break;
		}

		if ($attr) {
			$target = substr($target, 1);
			$out = "<div {$attr}='{$target}'></div>";
		}

		$this->output->set_output($out);
	}


}
