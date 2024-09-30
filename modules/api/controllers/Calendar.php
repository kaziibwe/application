<?php
defined('BASEPATH') or exit('No direct script access allowed');

require __DIR__ . '/REST_Controller.php';

class Calendar extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @api {get} api/calendar/ Get All Calendar Events
     * @apiName GetCalendarEvents
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiGroup Calendar Events
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "eventid": "1",
     *             "title": "Hello",
     *             "description": "test",
     *             "userid": "1",
     *             "start": "2023-12-12 07:00:00",
     *             "end": 2023-12-12 07:00:00,
     *             "public": "1",
     *             "color": "#03a9f4",
     *             "isstartnotified": "0",
     *             "reminder_before": "30",
     *             "reminder_before_type": "minutes"
     *         },
     *         {
     *             "eventid": "2",
     *             "title": "Hello2",
     *             "description": "test2",
     *             "userid": "2",
     *             "start": "2022-12-12 07:00:00",
     *             "end": 2022-12-12 07:00:00,
     *             "public": "0",
     *             "color": "#03a9f4",
     *             "isstartnotified": "0",
     *             "reminder_before": "3",
     *             "reminder_before_type": "hours"
     *         }
     *     ]
     *
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data were found"
     *     }
     */
	 
    /**
     * @api {get} api/calendar/:id Request Specific Event Information
     * @apiName GetCalendarEvent
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiGroup Calendar Events
     *
     * @apiParam {id} id Event data by id.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "eventid": "1",
     *             "title": "Hello",
     *             "description": "test",
     *             "userid": "1",
     *             "start": "2023-12-12 07:00:00",
     *             "end": 2023-12-12 07:00:00,
     *             "public": "1",
     *             "color": "#03a9f4",
     *             "isstartnotified": "0",
     *             "reminder_before": "30",
     *             "reminder_before_type": "minutes"
     *         }
     *     ]
     *
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data were found"
     *     }
     */
    public function data_get($id = '')
    {
        $data = $this->Api_model->get_table('events', $id);

        if ($data) {
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * @api {post} api/calendar/ Create a new Calendar Event
     * @apiName PostCalendarEvent
     * @apiGroup Calendar Events
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiParam {String} title Required event title.
     * @apiParam {String} description Optional event description.
     * @apiParam {Date} start Required event start date.
     * @apiParam {Date} start Optional event end date.
     * @apiParam {String} reminder_before_type Required value of reminder before type.
     * @apiParam {Number} reminder_before Required value of reminder before. 
     * @apiParam {String} color Optional event color.
     * @apiParam {Number} userid Required user id.
     * @apiParam {Number} isstartnotified Required isstartnotified status.
     * @apiParam {Number} public Required public status.
     * @apiParamExample {Multipart Form} Request-Example:
     *     'title' => string 'Hello'
     *     'start' => date '2023/12/12 07:00'
     *     'end' => date '2023/12/12 07:00'
     *     'reminder_before' => number '10'
     *     'reminder_before_type' => string 'minutes'
     *     'color' => string 'red'
     *     'description' => string 'for test'
     *     'userid' => number '1'
     *     'public' => number '1' (0/1)
     *     'isstartnotified' => number '0'
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     *  {
     *      "status": true,
     *      "message": "Data Added Successfully"
     *  }
     *
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *  {
     *      "status": false,
     *      "message": "Data Creation Failed"
     *  }
     */
    public function data_post()
    {
        $data = $this->input->post();

        if (empty($data['color'])) {
            $data['color'] = '#28B8DA';
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('start', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('end', 'End Date', 'trim');
        $this->form_validation->set_rules('reminder_before', 'Value', 'numeric|required');
        $this->form_validation->set_rules('reminder_before_type', 'reminder_type', 'trim|required');
        $this->form_validation->set_rules('color', 'Event Color', 'trim');
        $this->form_validation->set_rules('userid', 'Userid', 'numeric|required');
        $this->form_validation->set_rules('isstartnotified', 'Isstartnotified', 'numeric|required');
        $this->form_validation->set_rules('public', 'Public', 'numeric|required');
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {

            $id = $this->Api_model->event($data);

            if ($id > 0 && !empty($id)) {
                $message = array('status' => TRUE, 'message' => 'Data Added Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                $message = array('status' => FALSE, 'message' => 'Data Add Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {put} api/calendar/:id Update a Calendar Event
     * @apiName UpdateCalendarEvent
     * @apiGroup Calendar Events
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiParam {id} unique ID for update data.
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "title": "Hello",
     *      "start": "2023/12/12 07:00",
     *      "end": "2023/12/12 07:00",
     *      "reminder_before": "10",
     *      "reminder_before_type": "minutes",
     *      "color": "red",
     *      "description": "for test",
     *      "userid":6,
     *      "public":1,
     *      "isstartnotified":1
     *  }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Data Update Successful."
     *     }	
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *  {
     *       "status": false,
     *       "message": "Data Update Fail"
     *  }
     */
    public function data_put($id = '')
    {
        $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);

        if (empty($_POST) || !isset($_POST)) {
            $message = array('status' => FALSE, 'message' => 'Data Not Acceptable OR Not Provided');
            $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
        }

        $this->form_validation->set_data($_POST);

        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid data or missing Send ID. please provide updated data ID.');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $_POST['eventid'] = $id;
            $update_data = $this->input->post();

            $data = $_POST;
            $output = $this->Api_model->event($data);

            if ($output > 0 && !empty($output)) {
                $message = array('status' => TRUE, 'message' => 'Data Update Successful.');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                $message = array('status' => FALSE, 'message' => 'Data Update Fail.');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
    /**
     * @api {delete} api/calendar/:id Delete a Calendar Event
     * @apiVersion 0.3.0
     * @apiName DeleteCalendarEvent
     * @apiGroup Calendar Events
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiParam {Number} ID ID for data deletion.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Data Deleted Successfully"
     *     }
     *
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Data Delete Fail"
     *     }
     */
 
    public function data_delete($id = '')
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('utilities_model');
            $output = $this->utilities_model->delete_event($id);

            if ($output === TRUE) {
                $message = array('status' => TRUE, 'message' => 'Delete Successful.');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                $message = array('status' => FALSE, 'message' => 'Delete Fail.');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
