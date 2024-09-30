<?php

defined("BASEPATH") or exit("No direct script access allowed");

class Resourcebooking_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_resource_group($data)
    {
        $date = date("Y-m-d");

        $data["date_create"] = $date;

        $this->db->insert("tblresource_group", $data);

        $resource_group_id = $this->db->insert_id();

        if ($resource_group_id) {
            return $resource_group_id;
        }

        return false;
    }

    public function update_resource_group($data, $id)
    {
        $date = date("Y-m-d");

        $data["date_create"] = $date;

        $this->db->where("id", $id);
        $this->db->update("tblresource_group", $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_resource_group($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("tblresource_group");

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_resource_group($group = "")
    {
        if ($group != "") {
            $this->db->where("id", $group);

            return $this->db->get("tblresource_group")->row();
        } else {
            return $this->db->get("tblresource_group")->result_array();
        }
    }

    public function add_resource($data)
    {
        $this->db->insert("tblresource", $data);

        $resource_group_id = $this->db->insert_id();

        if ($resource_group_id) {
            return $resource_group_id;
        }

        return false;
    }

    public function update_resource($data, $id)
    {
        $this->db->where("id", $id);
        $this->db->update("tblresource", $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_resource($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("tblresource");

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_resource($id = "")
    {
        if ($id != "") {
            $this->db->where("id", $id);

            $resource = $this->db->get("tblresource")->row();

            return $resource;
        } else {
            return $this->db->get("tblresource")->result_array();
        }
    }

    public function get_resource_by_status($status = "")
    {
        $this->db->where("status", $status);

        $resource = $this->db->get("tblresource")->result_array();

        return $resource;
    }

    public function get_resource_group_by_id($id)
    {
        $this->db->where("id", $id);

        $resource_group = $this->db->get("tblresource_group")->row();

        return $resource_group;
    }

    public function get_resource_by_group($group, $status = "")
    {
        $this->db->where("resource_group", $group);
        $this->db->where("status", $status);

        $resource = $this->db->get("tblresource")->result_array();

        return $resource;
    }

    public function get_resource_activity_now($resource)
    {
        return $this->db->query("select * from tblbooking where (resource = " . $resource . ') and ((start_time >= "' . date("Y-m-d H:i:s") . '" and end_time >= "' . date("Y-m-d H:i:s") . '" ) or (start_time <= "' . date("Y-m-d H:i:s") . '" and end_time >= "' . date("Y-m-d H:i:s") . '" )) and status = 2')->result_array();
    }

    public function get_list_follower_by_booking($booking)
    {
        return $this->db->query("select follower from tblbooking_follower where booking = " . $booking)->result_array();
    }

    public function add_booking($data)
    {
        if (isset($data["follower"])) {
            $follower = $data["follower"];

            unset($data["follower"]);
        }

        $resources = $this->get_resource($data["resource"]);

        if ($resources->approved == 0) {
            $data["status"] = 2;
        } elseif ($resources->approved != 0) {
            $data["status"] = 1;
        }

        $data["start_time"] = to_sql_date($data["start_time"], true);

        $data["end_time"] = to_sql_date($data["end_time"], true);

        $this->db->insert("tblbooking", $data);

        $id = $this->db->insert_id();

        if ($id) {
            $additional_data = $data["purpose"];
            $mes_approver = _l("mes_approver");
            $mes_follower = _l("mes_follower");
            $link = "resourcebooking/booking/" . $id;

            if ($resources->manager != 0) {
                if (get_staff_user_id() != $resources->manager) {
                    $staff = $this->staff_model->get($resources->manager);

                    $notified = add_notification([
                        "description" => $mes_approver,
                        "touserid" => $staff->staffid,
                        "link" => $link,
                        "additional_data" => serialize([$additional_data]),
                    ]);

                    if ($notified) {
                        pusher_trigger_notification([$staff->staffid]);
                    }
                }
            }

            if (isset($follower)) {
                foreach ($follower as $follow) {
                    $this->db->insert("tblbooking_follower", [
                        "booking" => $id,
                        "follower" => $follow,
                    ]);
                }

                foreach ($follower as $value) {
                    if (get_staff_user_id() != $value) {
                        $staff = $this->staff_model->get($value);

                        $notified = add_notification([
                            "description" => $mes_follower,
                            "touserid" => $staff->staffid,
                            "link" => $link,
                            "additional_data" => serialize([$additional_data]),
                        ]);

                        if ($notified) {
                            pusher_trigger_notification([$staff->staffid]);
                        }
                    }
                }
            }

            return $id;
        }

        return false;
    }

    public function update_booking($data, $id)
    {
        if (isset($data["follower"])) {
            $follower = $data["follower"];

            unset($data["follower"]);
        }

        $data["start_time"] = to_sql_date($data["start_time"], true);
        $data["end_time"] = to_sql_date($data["end_time"], true);
        $this->db->where("id", $id);
        $this->db->update("tblbooking", $data);

        if (isset($follower)) {
            $this->db->where("booking", $id);

            $this->db->delete("tblbooking_follower");

            foreach ($follower as $follow) {
                $this->db->insert("tblbooking_follower", [
                    "booking" => $id,
                    "follower" => $follow,
                ]);
            }
        }
    }

    public function delete_booking($id)
    {
        $this->db->where("booking", $id);
        $this->db->delete("tblbooking_follower");
        $this->db->where("id", $id);
        $this->db->delete("tblbooking");

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_booking($id = "")
    {
        if ($id != "") {
            $this->db->where("id", $id);

            $booking = $this->db->get("tblbooking")->row();
        } else {
            $booking = $this->db->get("tblbooking")->result_array();
        }

        return $booking;
    }

    public function get_time_booking($start_time, $end_time)
    {
        $h_start = date_format(date_create($start_time), " H:i");
        $date_start = date_format(date_create($start_time), " d/m/Y");
        $h_end = date_format(date_create($end_time), " H:i");
        $date_end = date_format(date_create($end_time), " d/m/Y");

        $html = '<div class="col-md-5 card border-success mb-3 rbmaxwidth18">
        <div class="card-body text-danger">';
        $html .= '<h3 class="card-title">' . $h_start . "</h3>";
        $html .= '<p class="card-text"> ' . $date_start . "</p>";
        $html .= "</div></div>";
        $html .= '<div class="col-md-5 card border-success mb-3 rbmaxwidth18">
        <div class="card-body text-danger">';
        $html .= '<h3 class="card-title">' . $h_end . "</h3>";
        $html .= '<p class="card-text"> ' . $date_end . "</p>";
        $html .= "</div></div>";

        return $html;
    }

    public function approve_booking($status, $booking)
    {
        $this->db->where("id", $booking);

        $this->db->update(db_prefix() . "booking", ["status" => $status]);

        $ca = $this->db->affected_rows();

        $follower = $this->get_list_follower_by_booking($booking);
        $bookings = $this->get_booking($booking);
        $additional_data = $bookings->purpose;
        $mes_orderer = _l("mes_orderer");

        $mes_follower_approved = _l("mes_follower_approved");

        $link = "resourcebooking/booking/" . $booking;

        if ($bookings->orderer != 0) {
            if (get_staff_user_id() != $bookings->orderer) {
                $staff = $this->staff_model->get($bookings->orderer);

                $notified = add_notification([
                    "description" => $mes_orderer,
                    "touserid" => $staff->staffid,
                    "link" => $link,
                    "additional_data" => serialize([$additional_data]),
                ]);

                if ($notified) {
                    pusher_trigger_notification([$staff->staffid]);
                }
            }
        }

        if (isset($follower)) {
            foreach ($follower as $value) {
                if (get_staff_user_id() != $value["follower"]) {
                    $staff = $this->staff_model->get($value["follower"]);

                    $notified = add_notification([
                        "description" => $mes_follower_approved,
                        "touserid" => $staff->staffid,
                        "link" => $link,
                        "additional_data" => serialize([$additional_data]),
                    ]);

                    if ($notified) {
                        pusher_trigger_notification([$staff->staffid]);
                    }
                }
            }
        }

        if ($ca > 0) {
            return true;
        }

        return false;
    }

    public function check_approve_booking(
        $resource,
        $booking,
        $start_time,
        $end_time,
        $type
    ) {
        $check_approved = $this->db->query("select * from tblbooking where resource = " . $resource . " and id != " . $booking . ' and status = 2 and ( ((start_time <= "' . $start_time . '" and end_time >= "' . $start_time . '" ) or (start_time <= "' . $end_time . '" and end_time >= "' . $end_time . '" )) or (start_time >= "' . $start_time . '" and end_time <= "' . $end_time . '"))')->result_array();
        $check_sending = $this->db->query("select * from tblbooking where resource = " . $resource . " and id != " . $booking . ' and status = 1 and ( ((start_time <= "' . $start_time . '" and end_time >= "' . $start_time . '" ) or (start_time <= "' . $end_time . '" and end_time >= "' . $end_time . '" )) or (start_time >= "' . $start_time . '" and end_time <= "' . $end_time . '"))')->result_array();

        if ($type == "sending") {
            return $check_sending;
        } elseif ($type == "approved") {
            return $check_approved;
        }
    }

    public function check_approve_booking_resource_no_manager(
        $resource,
        $start_time,
        $end_time
    ) {
        $check_approved = $this->db->query("select * from tblbooking where resource = " . $resource . ' and status = 2 and ( ((start_time <= "' . $start_time . '" and end_time >= "' . $start_time . '" ) or (start_time <= "' . $end_time . '" and end_time >= "' . $end_time . '" )) or (start_time >= "' . $start_time . '" and end_time <= "' . $end_time . '"))')->result_array();

        return $check_approved;
    }

    public function get_calendar_data($start, $end, $filters = false)
    {
        $data = [];

        $list_booking = $this->get_booking();

        foreach ($list_booking as $booking) {
            $resource = $this->get_resource($booking["resource"]);

            $calendar["title"] = $resource->resource_name . " - " . $booking["purpose"];
            $calendar["color"] = $resource->color;

            $calendar["_tooltip"] = $resource->resource_name . " - " . $booking["purpose"] . "\n" . " Start: " . _dt($booking["start_time"]) . "\n" . " End: " . _dt($booking["end_time"]);

            $calendar["url"] = admin_url("resourcebooking/booking/" . $booking["id"]);

            $calendar["start"] = $booking["start_time"];
            $calendar["end"] = $booking["end_time"];

            array_push($data, $calendar);
        }

        return hooks()->apply_filters("calendar_data", $data, [
            "start" => $start,
            "end" => $end,
        ]);
    }

    public function get_booking_by_resource($resource)
    {
        $this->db->where("resource", $resource);
        $this->db->where("status", 2);

        $booking = $this->db->get("tblbooking")->result_array();

        return $booking;
    }

    public function get_booking_by_resource_activiti($resource)
    {
        return $this->db ->query("select * from tblbooking where resource = " . $resource . ' and status = 2 and end_time <= "' . date("Y-m-d H:i:s") . '" order by end_time DESC'
            )->result_array();
    }

    public function get_myboking($staff, $status)
    {
        if ($status == "total") {
            $this->db->where("orderer", $staff);
            $total = $this->db->get("tblbooking")->result_array();

            return $total;
        } elseif ($status == "sending") {
            $this->db->where("status", 1);
            $this->db->where("orderer", $staff);
            $sending = $this->db->get("tblbooking")->result_array();

            return $sending;
        } elseif ($status == "approved") {
            $this->db->where("status", 2);
            $this->db->where("orderer", $staff);
            $approved = $this->db->get("tblbooking")->result_array();

            return $approved;
        } elseif ($status == "reject") {
            $this->db->where("status", 3);
            $this->db->where("orderer", $staff);
            $reject = $this->db->get("tblbooking")->result_array();

            return $reject;
        }
    }

    public function get_recently_booking($staff)
    {
        return $this->db->query("select * from tblbooking where orderer = " .
            $staff . " order by start_time, end_time ASC limit 5"
        )->result_array();
    }

    public function get_apr_booking($staff)
    {
        return $this->db->query('SELECT bk.id, bk.purpose, bk.resource, bk.start_time, bk.end_time, bk.status FROM tblbooking bk LEFT JOIN tblresource rs on rs.id = bk.resource
            where rs.manager = ' . $staff . " and rs.approved = 1 and bk.status = 1"
        )->result_array();
    }

    public function get_group_resourcebooking($group)
    {
        return $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name, rs.resource_name from tblbooking bk
            left join tblresource rs on rs.id = bk.resource
            left join tblresource_group rsg on rsg.id = rs.resource_group
            where rsg.id = ' . $group
        )->result_array();
    }

    public function get_group_resourcebooking_approve($group)
    {
        return $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
            left join tblresource rs on rs.id = bk.resource
            left join tblresource_group rsg on rsg.id = rs.resource_group
            where rsg.id = ' . $group . " and bk.status = 2"
        )->result_array();
    }

    public function get_pie_chart_rs_booking()
    {
        $gr = $this->get_resource_group();

        $_data = [];

        $list = [];

        $total_bk = $this->get_booking();

        $colors = [
            "#79352c",
            "#521250",
            "#c79ed2",
            "#d6dd92",
            "#e33e52",
            "#b2be57",
            "#fa06ec",

            "#250662",
            "#cb5bea",
            "#228916",
            "#ac3e1b",
            "#df514a",
            "#539397",
            "#880977",

            "#f205e6",
            "#1c0365",
            "#14a9ad",
            "#4ca2f9",
            "#a4e43f",
            "#d298e2",
            "#6119d0",

            "#f697c1",
            "#ba96ce",
            "#679c9d",
            "#c6c42c",
            "#5d2c52",
            "#48b41b",
            "#e1cf3b",

            "#5be4f0",
            "#57c4d8",
            "#a4d17a",
            "#225b8",
            "#be608b",
            "#96b00c",
            "#088baf",

            "#63b598",
            "#ce7d78",
            "#ea9e70",
            "#a48a9e",
            "#c6e1e8",
            "#648177",
            "#0d5ac1",

            "#d2737d",
            "#c0a43c",
            "#f2510e",
            "#651be6",
            "#79806e",
            "#61da5e",
            "#cd2f00",

            "#9348af",
            "#01ac53",
            "#c5a4fb",
            "#996635",
            "#b11573",
            "#4bb473",
            "#75d89e",

            "#2f3f94",
            "#2f7b99",
            "#da967d",
            "#34891f",
            "#b0d87b",
            "#ca4751",
            "#7e50a8",

            "#c4d647",
            "#e0eeb8",
            "#11dec1",
            "#289812",
            "#566ca0",
            "#ffdbe1",
            "#2f1179",

            "#935b6d",
            "#916988",
            "#513d98",
            "#aead3a",
            "#9e6d71",
            "#4b5bdc",
            "#0cd36d",

            "#f158bf",
            "#e145ba",
            "#ee91e3",
            "#05d371",
            "#5426e0",
            "#4834d0",
            "#802234",

            "#6749e8",
            "#0971f0",
            "#8fb413",
            "#b2b4f0",
            "#c3c89d",
            "#c9a941",
            "#41d158",

            "#fb21a3",
            "#51aed9",
            "#5bb32d",
            "#807fb",
            "#21538e",
            "#89d534",
            "#d36647",

            "#7fb411",
            "#0023b8",
            "#3b8c2a",
            "#986b53",
            "#f50422",
            "#983f7a",
            "#ea24a3",

            "#79352c",
            "#521250",
            "#c79ed2",
            "#d6dd92",
            "#e33e52",
            "#b2be57",
            "#fa06ec",

            "#1bb699",
            "#6b2e5f",
            "#64820f",
            "#1c271",
            "#21538e",
            "#89d534",
            "#d36647",

            "#7fb411",
            "#0023b8",
            "#3b8c2a",
            "#986b53",
            "#f50422",
            "#983f7a",
            "#ea24a3",

            "#1bb699",
            "#6b2e5f",
            "#64820f",
            "#1c271",
            "#9cb64a",
            "#996c48",
            "#9ab9b7",

            "#06e052",
            "#e3a481",
            "#0eb621",
            "#fc458e",
            "#b2db15",
            "#aa226d",
            "#792ed8",

            "#73872a",
            "#520d3a",
            "#cefcb8",
            "#a5b3d9",
            "#7d1d85",
            "#c4fd57",
            "#f1ae16",

            "#8fe22a",
            "#ef6e3c",
            "#243eeb",
            "#1dc18",
            "#dd93fd",
            "#3f8473",
            "#e7dbce",

            "#421f79",
            "#7a3d93",
            "#635f6d",
            "#93f2d7",
            "#9b5c2a",
            "#15b9ee",
            "#0f5997",

            "#409188",
            "#911e20",
            "#1350ce",
            "#10e5b1",
            "#fff4d7",
            "#cb2582",
            "#ce00be",

            "#32d5d6",
            "#17232",
            "#608572",
            "#c79bc2",
            "#00f87c",
            "#77772a",
            "#6995ba",

            "#fc6b57",
            "#f07815",
            "#8fd883",
            "#060e27",
            "#96e591",
            "#21d52e",
            "#d00043",

            "#b47162",
            "#1ec227",
            "#4f0f6f",
            "#1d1d58",
            "#947002",
            "#bde052",
            "#e08c56",

            "#28fcfd",
            "#bb09b",
            "#36486a",
            "#d02e29",
            "#1ae6db",
            "#3e464c",
            "#a84a8f",

            "#911e7e",
            "#3f16d9",
            "#0f525f",
            "#ac7c0a",
            "#b4c086",
            "#c9d730",
            "#30cc49",

            "#3d6751",
            "#fb4c03",
            "#640fc1",
            "#62c03e",
            "#d3493a",
            "#88aa0b",
            "#406df9",

            "#615af0",
            "#4be47",
            "#2a3434",
            "#4a543f",
            "#79bca0",
            "#a8b8d4",
            "#00efd4",

            "#7ad236",
            "#7260d8",
            "#1deaa7",
            "#06f43a",
            "#823c59",
            "#e3d94c",
            "#dc1c06",

            "#f53b2a",
            "#b46238",
            "#2dfff6",
            "#a82b89",
            "#1a8011",
            "#436a9f",
            "#1a806a",

            "#4cf09d",
            "#c188a2",
            "#67eb4b",
            "#b308d3",
            "#fc7e41",
            "#af3101",
            "#ff065",

            "#71b1f4",
            "#a2f8a5",
            "#e23dd0",
            "#d3486d",
            "#00f7f9",
            "#474893",
            "#3cec35",

            "#1c65cb",
            "#5d1d0c",
            "#2d7d2a",
            "#ff3420",
            "#5cdd87",
            "#a259a4",
            "#e4ac44",

            "#1bede6",
            "#8798a4",
            "#d7790f",
            "#b2c24f",
            "#de73c2",
            "#d70a9c",
            "#25b67",

            "#88e9b8",
            "#c2b0e2",
            "#86e98f",
            "#ae90e2",
            "#1a806b",
            "#436a9e",
            "#0ec0ff",

            "#f812b3",
            "#b17fc9",
            "#8d6c2f",
            "#d3277a",
            "#2ca1ae",
            "#9685eb",
            "#8a96c6",

            "#dba2e6",
            "#76fc1b",
            "#608fa4",
            "#20f6ba",
            "#07d7f6",
            "#dce77a",
            "#77ecca",
        ];

        $dem = 0;

        foreach ($gr as $g) {
            $dem++;

            $bk_gr = $this->get_group_resourcebooking($g["id"]);

            if (count($total_bk) != 0) {
                $list = [
                    "name" => $g["group_name"],
                    "color" => $colors[$dem],
                    "y" => (count($bk_gr) / count($total_bk)) * 100,
                ];
            } else {
                $list = [
                    "name" => $g["group_name"],
                    "color" => $colors[$dem],
                    "y" => 0,
                ];
            }

            array_push($_data, $list);
        }

        return $_data;
    }

    public function get_gr_line_col_chart($type)
    {
        $gr = $this->get_resource_group();

        if ($type == "name") {
            $list = [];

            foreach ($gr as $g) {
                array_push($list, $g["group_name"]);
            }

            return $list;
        }

        if ($type == "data_col") {
            $list = [];

            foreach ($gr as $g) {
                $bk_gr = $this->get_group_resourcebooking_approve($g["id"]);

                array_push($list, count($bk_gr));
            }

            return $list;
        }

        if ($type == "data_line") {
            $list = [];

            foreach ($gr as $g) {
                $bk_gr = $this->db->query('select SUM(ROUND(TIMESTAMPDIFF(minute, start_time, end_time)/60,2) ) as `hour_use`
                    from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where rsg.id = ' . $g["id"] . " and bk.status = 2"
                )->row();

                array_push($list, (float) $bk_gr->hour_use);
            }

            return $list;
        }
    }

    public function get_month_booking_chart($type)
    {
        if ($type == "data_col") {
            $list = [];

            for ($i = 1; $i < 13; $i++) {
                $bk_month = $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = 2 and  (month(bk.start_time) = ' . $i . " or month(bk.end_time) = " . $i . ")"
                )->result_array();

                array_push($list, count($bk_month));
            }

            return $list;
        }

        if ($type == "data_line") {
            $list = [];

            for ($i = 1; $i < 13; $i++) {
                $bk_month = $this->db->query('select SUM(ROUND(TIMESTAMPDIFF(minute, start_time, end_time)/60,2) ) as `hour_use`
                    from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = 2 and  (month(bk.start_time) = ' . $i . " or month(bk.end_time) = " . $i . ")"
                )->row();

                array_push($list, (float) $bk_month->hour_use);
            }

            return $list;
        }
    }

    public function get_pie_chart_rs_booking_status()
    {
        $_data = [];

        $list = [];

        $total_bk = $this->get_booking();

        for ($i = 1; $i < 4; $i++) {
            $bk_gr = $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = ' . $i
                )->result_array();

            if ($i == 1) {
                if (count($total_bk) != 0) {
                    $list = [
                        "name" => _l("sending"),
                        "color" => "#03A9F4",
                        "y" => (count($bk_gr) / count($total_bk)) * 100,
                    ];
                } else {
                    $list = [
                        "name" => _l("sending"),
                        "color" => "#03A9F4",
                        "y" => 0,
                    ];
                }
            } elseif ($i == 2) {
                if (count($total_bk) != 0) {
                    $list = [
                        "name" => _l("approved"),
                        "color" => "#84c529",
                        "y" => (count($bk_gr) / count($total_bk)) * 100,
                    ];
                } else {
                    $list = [
                        "name" => _l("approved"),
                        "color" => "#84c529",
                        "y" => 0,
                    ];
                }
            } elseif ($i == 3) {
                if (count($total_bk) != 0) {
                    $list = [
                        "name" => _l("reject"),
                        "color" => "#ff2d42",
                        "y" => (count($bk_gr) / count($total_bk)) * 100,
                    ];
                } else {
                    $list = [
                        "name" => _l("reject"),
                        "color" => "#ff2d42",
                        "y" => 0,
                    ];
                }
            }

            array_push($_data, $list);
        }

        return $_data;
    }

    public function get_resource_by_group_pie_chart($group)
    {
        $rs = $this->get_resource_by_group($group, "active");

        $bk_gr = $this->get_group_resourcebooking($group);

        $list = [];

        $_data = [];

        $colors = [
            "#79352c",
            "#521250",
            "#c79ed2",
            "#d6dd92",
            "#e33e52",
            "#b2be57",
            "#fa06ec",

            "#250662",
            "#cb5bea",
            "#228916",
            "#ac3e1b",
            "#df514a",
            "#539397",
            "#880977",

            "#f205e6",
            "#1c0365",
            "#14a9ad",
            "#4ca2f9",
            "#a4e43f",
            "#d298e2",
            "#6119d0",

            "#f697c1",
            "#ba96ce",
            "#679c9d",
            "#c6c42c",
            "#5d2c52",
            "#48b41b",
            "#e1cf3b",

            "#5be4f0",
            "#57c4d8",
            "#a4d17a",
            "#225b8",
            "#be608b",
            "#96b00c",
            "#088baf",

            "#63b598",
            "#ce7d78",
            "#ea9e70",
            "#a48a9e",
            "#c6e1e8",
            "#648177",
            "#0d5ac1",

            "#d2737d",
            "#c0a43c",
            "#f2510e",
            "#651be6",
            "#79806e",
            "#61da5e",
            "#cd2f00",

            "#9348af",
            "#01ac53",
            "#c5a4fb",
            "#996635",
            "#b11573",
            "#4bb473",
            "#75d89e",

            "#2f3f94",
            "#2f7b99",
            "#da967d",
            "#34891f",
            "#b0d87b",
            "#ca4751",
            "#7e50a8",

            "#c4d647",
            "#e0eeb8",
            "#11dec1",
            "#289812",
            "#566ca0",
            "#ffdbe1",
            "#2f1179",

            "#935b6d",
            "#916988",
            "#513d98",
            "#aead3a",
            "#9e6d71",
            "#4b5bdc",
            "#0cd36d",

            "#f158bf",
            "#e145ba",
            "#ee91e3",
            "#05d371",
            "#5426e0",
            "#4834d0",
            "#802234",

            "#6749e8",
            "#0971f0",
            "#8fb413",
            "#b2b4f0",
            "#c3c89d",
            "#c9a941",
            "#41d158",

            "#fb21a3",
            "#51aed9",
            "#5bb32d",
            "#807fb",
            "#21538e",
            "#89d534",
            "#d36647",

            "#7fb411",
            "#0023b8",
            "#3b8c2a",
            "#986b53",
            "#f50422",
            "#983f7a",
            "#ea24a3",

            "#79352c",
            "#521250",
            "#c79ed2",
            "#d6dd92",
            "#e33e52",
            "#b2be57",
            "#fa06ec",

            "#1bb699",
            "#6b2e5f",
            "#64820f",
            "#1c271",
            "#21538e",
            "#89d534",
            "#d36647",

            "#7fb411",
            "#0023b8",
            "#3b8c2a",
            "#986b53",
            "#f50422",
            "#983f7a",
            "#ea24a3",

            "#1bb699",
            "#6b2e5f",
            "#64820f",
            "#1c271",
            "#9cb64a",
            "#996c48",
            "#9ab9b7",

            "#06e052",
            "#e3a481",
            "#0eb621",
            "#fc458e",
            "#b2db15",
            "#aa226d",
            "#792ed8",

            "#73872a",
            "#520d3a",
            "#cefcb8",
            "#a5b3d9",
            "#7d1d85",
            "#c4fd57",
            "#f1ae16",

            "#8fe22a",
            "#ef6e3c",
            "#243eeb",
            "#1dc18",
            "#dd93fd",
            "#3f8473",
            "#e7dbce",

            "#421f79",
            "#7a3d93",
            "#635f6d",
            "#93f2d7",
            "#9b5c2a",
            "#15b9ee",
            "#0f5997",

            "#409188",
            "#911e20",
            "#1350ce",
            "#10e5b1",
            "#fff4d7",
            "#cb2582",
            "#ce00be",

            "#32d5d6",
            "#17232",
            "#608572",
            "#c79bc2",
            "#00f87c",
            "#77772a",
            "#6995ba",

            "#fc6b57",
            "#f07815",
            "#8fd883",
            "#060e27",
            "#96e591",
            "#21d52e",
            "#d00043",

            "#b47162",
            "#1ec227",
            "#4f0f6f",
            "#1d1d58",
            "#947002",
            "#bde052",
            "#e08c56",

            "#28fcfd",
            "#bb09b",
            "#36486a",
            "#d02e29",
            "#1ae6db",
            "#3e464c",
            "#a84a8f",

            "#911e7e",
            "#3f16d9",
            "#0f525f",
            "#ac7c0a",
            "#b4c086",
            "#c9d730",
            "#30cc49",

            "#3d6751",
            "#fb4c03",
            "#640fc1",
            "#62c03e",
            "#d3493a",
            "#88aa0b",
            "#406df9",

            "#615af0",
            "#4be47",
            "#2a3434",
            "#4a543f",
            "#79bca0",
            "#a8b8d4",
            "#00efd4",

            "#7ad236",
            "#7260d8",
            "#1deaa7",
            "#06f43a",
            "#823c59",
            "#e3d94c",
            "#dc1c06",

            "#f53b2a",
            "#b46238",
            "#2dfff6",
            "#a82b89",
            "#1a8011",
            "#436a9f",
            "#1a806a",

            "#4cf09d",
            "#c188a2",
            "#67eb4b",
            "#b308d3",
            "#fc7e41",
            "#af3101",
            "#ff065",

            "#71b1f4",
            "#a2f8a5",
            "#e23dd0",
            "#d3486d",
            "#00f7f9",
            "#474893",
            "#3cec35",

            "#1c65cb",
            "#5d1d0c",
            "#2d7d2a",
            "#ff3420",
            "#5cdd87",
            "#a259a4",
            "#e4ac44",

            "#1bede6",
            "#8798a4",
            "#d7790f",
            "#b2c24f",
            "#de73c2",
            "#d70a9c",
            "#25b67",

            "#88e9b8",
            "#c2b0e2",
            "#86e98f",
            "#ae90e2",
            "#1a806b",
            "#436a9e",
            "#0ec0ff",

            "#f812b3",
            "#b17fc9",
            "#8d6c2f",
            "#d3277a",
            "#2ca1ae",
            "#9685eb",
            "#8a96c6",

            "#dba2e6",
            "#76fc1b",
            "#608fa4",
            "#20f6ba",
            "#07d7f6",
            "#dce77a",
            "#77ecca",
        ];

        $dem = 0;

        foreach ($rs as $r) {
            $dem++;

            $rs_in_gr = $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name, rs.resource_name from tblbooking bk
                left join tblresource rs on rs.id = bk.resource
                left join tblresource_group rsg on rsg.id = rs.resource_group
                where rsg.id = ' . $group . " and rs.id = " . $r["id"]
            )->result_array();

            if (count($bk_gr) != 0) {
                $list = [
                    "name" => $r["resource_name"],
                    "color" => $colors[$dem],
                    "y" => (count($rs_in_gr) / count($bk_gr)) * 100,
                ];
            } else {
                $list = [
                    "name" => $r["resource_name"],
                    "color" => $colors[$dem],
                    "y" => 0,
                ];
            }

            array_push($_data, $list);
        }

        return $_data;
    }

    public function get_resource_by_group_col_line_chart($group, $type)
    {
        $rs = $this->get_resource_by_group($group, "active");

        if ($type == "name") {
            $list = [];

            foreach ($rs as $g) {
                array_push($list, $g["resource_name"]);
            }

            return $list;
        }

        if ($type == "col") {
            $list = [];

            foreach ($rs as $g) {
                $bk_gr = $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where rsg.id = ' . $group . " and bk.status = 2 and bk.resource = " . $g["id"]
                )->result_array();

                array_push($list, count($bk_gr));
            }

            return $list;
        }

        if ($type == "line") {
            $list = [];

            foreach ($rs as $g) {
                $bk_gr = $this->db->query('select SUM(ROUND(TIMESTAMPDIFF(minute, start_time, end_time)/60,2) ) as `hour_use`
                    from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where rsg.id = ' . $group . " and bk.status = 2 and rs.id = " . $g["id"]
                )->row();

                array_push($list, (float) $bk_gr->hour_use);
            }

            return $list;
        }
    }

    public function get_resource_filter_chart($resource, $type)
    {
        if ($type == "col") {
            $list = [];

            for ($i = 1; $i < 13; $i++) {
                $bk_month = $this->db->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = 2 and bk.resource = ' . $resource . " and (month(bk.start_time) = " . $i . " or month(bk.end_time) = " . $i . ")"
                )->result_array();

                array_push($list, count($bk_month));
            }

            return $list;
        }

        if ($type == "line") {
            $list = [];

            for ($i = 1; $i < 13; $i++) {
                $bk_month = $this->db->query('select SUM(ROUND(TIMESTAMPDIFF(minute, start_time, end_time)/60,2) ) as `hour_use`
                    from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = 2 and bk.resource = ' . $resource . " and (month(bk.start_time) = " . $i . " or month(bk.end_time) = " . $i . ")"
                )->row();

                array_push($list, (float) $bk_month->hour_use);
            }

            return $list;
        }
    }

    public function get_month_filter_chart($data, $type)
    {
        if ($type == "name") {
            $list = [];

            foreach ($data["month"] as $i) {
                array_push($list, _l("month_" . $i));
            }

            return $list;
        }

        if ($type == "col") {
            $list = [];

            foreach ($data["month"] as $i) {
                $bk_month = $this->db
                    ->query('select bk.id, bk.purpose, bk.resource, bk.orderer, bk.start_time, bk.end_time, bk.status, rsg.group_name from tblbooking bk
                        left join tblresource rs on rs.id = bk.resource
                        left join tblresource_group rsg on rsg.id = rs.resource_group
                        where bk.status = 2 and  (month(bk.start_time) = ' . $i . " or month(bk.end_time) = " . $i . ")"
                    )->result_array();

                array_push($list, count($bk_month));
            }

            return $list;
        }

        if ($type == "line") {
            $list = [];

            foreach ($data["month"] as $i) {
                $bk_month = $this->db->query('select SUM(ROUND(TIMESTAMPDIFF(minute, start_time, end_time)/60,2) ) as `hour_use`
                    from tblbooking bk
                    left join tblresource rs on rs.id = bk.resource
                    left join tblresource_group rsg on rsg.id = rs.resource_group
                    where bk.status = 2 and  (month(bk.start_time) = ' . $i . " or month(bk.end_time) = " . $i . ")")
                ->row();

                array_push($list, (float) $bk_month->hour_use);
            }

            return $list;
        }
    }

    public function get_booking_comments($id)
    {
        $task_comments_order = hooks()->apply_filters("task_comments_order", "DESC");

        $this->db->select("id,dateadded,content," . db_prefix() . "staff.firstname," . db_prefix() . "staff.lastname," . db_prefix() . "task_comments.staffid," . db_prefix() . 'task_comments.contact_id as contact_id,file_id,CONCAT(firstname, " ", lastname) as staff_full_name');
        $this->db->from(db_prefix() . "task_comments");
        $this->db->join(db_prefix() . "staff", db_prefix() . "staff.staffid = " . db_prefix() . "task_comments.staffid", "left");

        $this->db->where("taskid", $id);
        $this->db->where("type", "booking");
        $this->db->order_by("dateadded", $task_comments_order);

        $comments = $this->db->get()->result_array();

        $ids = [];

        foreach ($comments as $key => $comment) {
            array_push($ids, $comment["id"]);

            $comments[$key]["attachments"] = [];
        }

        if (count($ids) > 0) {
            $allAttachments = $this->get_booking_attachments($id, "task_comment_id IN (" . implode(",", $ids) . ")");

            foreach ($comments as $key => $comment) {
                foreach ($allAttachments as $attachment) {
                    if ($comment["id"] == $attachment["task_comment_id"]) {
                        $comments[$key]["attachments"][] = $attachment;
                    }
                }
            }
        }

        return $comments;
    }

    public function get_booking_attachments($booking, $where = [])
    {
        $this->db->select(implode(", ", prefixed_table_fields_array(db_prefix() . "files")) . ", " . db_prefix() . "task_comments.id as comment_file_id");
        $this->db->where(db_prefix() . "files.rel_id", $booking);
        $this->db->where(db_prefix() . "files.rel_type", "booking");

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != "")) {
            $this->db->where($where);
        }

        $this->db->join(db_prefix() . "task_comments", db_prefix() . "task_comments.file_id = " . db_prefix() . "files.id", "left");

        $this->db->join(db_prefix() . "booking", db_prefix() . "booking.id = " . db_prefix() . "files.rel_id");

        $this->db->order_by(db_prefix() . "files.dateadded", "desc");

        return $this->db->get(db_prefix() . "files")->result_array();
    }

    public function add_booking_comment($data)
    {
        if (is_client_logged_in()) {
            $data["staffid"] = 0;

            $data["contact_id"] = get_contact_user_id();
        } else {
            $data["staffid"] = get_staff_user_id();

            $data["contact_id"] = 0;
        }

        $this->db->insert(db_prefix() . "task_comments", [
            "taskid" => $data["booking"],
            "content" => is_client_logged_in()
                ? _strip_tags($data["content"])
                : $data["content"],
            "staffid" => $data["staffid"],
            "contact_id" => $data["contact_id"],
            "dateadded" => date("Y-m-d H:i:s"),
            "type" => "booking",
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    public function edit_comment($data)
    {
        // Check if user really creator
        $this->db->where("id", $data["id"]);

        $comment = $this->db->get(db_prefix() . "task_comments")->row();

        if ($comment->staffid == get_staff_user_id()) {
            $comment_added = strtotime($comment->dateadded);

            $minus_1_hour = strtotime("-1 hours");

            if (total_rows(db_prefix() . "files", ["task_comment_id" => $comment->id]) > 0) {
                $data["content"] .= "[task_attachment]";
            }

            $this->db->where("id", $data["id"]);

            $this->db->update(db_prefix() . "task_comments", ["content" => $data["content"]]);

            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        }
    }

    public function remove_comment($id, $force = false)
    {
        // Check if user really creator
        $this->db->where("id", $id);

        $comment = $this->db->get(db_prefix() . "task_comments")->row();

        if (!$comment) {
            return true;
        }

        if ($comment->staffid == get_staff_user_id() || $force === true) {
            $comment_added = strtotime($comment->dateadded);

            $minus_1_hour = strtotime("-1 hours");

            if (get_option("client_staff_add_edit_delete_task_comments_first_hour") == 0 || (get_option("client_staff_add_edit_delete_task_comments_first_hour") == 1 && $comment_added >= $minus_1_hour) || (is_admin() || $force === true)) {
                $this->db->where("id", $id);
                $this->db->delete(db_prefix() . "task_comments");

                if ($this->db->affected_rows() > 0) {
                    if ($comment->file_id != 0) {
                        $this->remove_booking_attachment($comment->file_id);
                    }

                    $commentAttachments = $this->get_booking_attachments($comment->taskid, "task_comment_id=" . $id);

                    foreach ($commentAttachments as $attachment) {
                        $this->remove_booking_attachment($attachment["id"]);
                    }

                    return true;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    public function remove_booking_attachment($id)
    {
        $comment_removed = false;

        $deleted = false;

        // Get the attachment
        $this->db->where("id", $id);

        $attachment = $this->db->get(db_prefix() . "files")->row();

        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = RESOURCEBOOKING_MODULE_UPLOAD_FOLDER . "/" . $attachment->rel_id . "/";

                $fullPath = $relPath . $attachment->file_name;

                unlink($fullPath);

                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);

                $thumbPath = $relPath . $fname . "_thumb." . $fext;

                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where("id", $attachment->id);
            $this->db->delete(db_prefix() . "files");

            if ($this->db->affected_rows() > 0) {
                $deleted = true;

                log_activity("Booking Attachment Deleted [BookingID: " . $attachment->rel_id . "]");
            }

            if (
                is_dir(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER . "/" . $attachment->rel_id)
            ) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER . "/" . $attachment->rel_id);

                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also

                    delete_dir(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER . "/" . $attachment->rel_id);
                }
            }
        }

        if ($deleted) {
            if ($attachment->task_comment_id != 0) {
                $total_comment_files = total_rows(db_prefix() . "files", [
                    "task_comment_id" => $attachment->task_comment_id,
                ]);

                if ($total_comment_files == 0) {
                    $this->db->where("id", $attachment->task_comment_id);

                    $comment = $this->db
                        ->get(db_prefix() . "task_comments")
                        ->row();

                    if ($comment) {
                        // Comment is empty and uploaded only with attachments
                        // Now all attachments are deleted, we need to delete the comment too
                        if (empty($comment->content) || $comment->content === "[task_attachment]" ) {
                            $this->db->where("id", $attachment->task_comment_id);

                            $this->db->delete(db_prefix() . "task_comments");

                            $comment_removed = $comment->id;
                        } else {
                            $this->db->query("UPDATE " . db_prefix() . "task_comments SET content = REPLACE(content, '[task_attachment]', '') WHERE id = " . $attachment->task_comment_id);
                        }
                    }
                }
            }

            $this->db->where("file_id", $id);

            $comment_attachment = $this->db->get(db_prefix() . "task_comments")->row();

            if ($comment_attachment) {
                $this->remove_comment($comment_attachment->id);
            }
        }

        return ["success" => $deleted, "comment_removed" => $comment_removed];
    }
}
