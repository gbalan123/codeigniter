<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Validation;

use Config\Database;
use InvalidArgumentException;

use DateTimeZone;
use DateTime;

use App\Models\School\Eventmodel;
use App\Models\Admin\Cmsmodel;
use App\Models\Teacher\Teachermodel;


/**
 * Validation Rules.
 */
class Rules
{
    function __construct() {

        $this->db = \Config\Database::connect();
        $this->cmsmodel = new Cmsmodel();
        $this->eventmodel = new Eventmodel();
        $this->teachermodel = new Teachermodel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->validation =  \Config\Services::validation();

        helper('downtime_helper');
        helper('is_email');

    }
    /**
     * The value does not match another field in $data.
     *
     * @param array $data Other field/value pairs
     */
    public function differs(?string $str, string $field, array $data): bool
    {
        if (strpos($field, '.') !== false) {
            return $str !== dot_array_search($field, $data);
        }

        return array_key_exists($field, $data) && $str !== $data[$field];
    }

    /**
     * Equals the static value provided.
     */
    public function equals(?string $str, string $val): bool
    {
        return $str === $val;
    }

    /**
     * Returns true if $str is $val characters long.
     * $val = "5" (one) | "5,8,12" (multiple values)
     */
    public function exact_length(?string $str, string $val): bool
    {
        $val = explode(',', $val);

        foreach ($val as $tmp) {
            if (is_numeric($tmp) && (int) $tmp === mb_strlen($str ?? '')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Greater than
     */
    public function greater_than(?string $str, string $min): bool
    {
        return is_numeric($str) && $str > $min;
    }

    /**
     * Equal to or Greater than
     */
    public function greater_than_equal_to(?string $str, string $min): bool
    {
        return is_numeric($str) && $str >= $min;
    }

    /**
     * Checks the database to see if the given value exist.
     * Can ignore records by field/value to filter (currently
     * accept only one filter).
     *
     * Example:
     *    is_not_unique[table.field,where_field,where_value]
     *    is_not_unique[menu.id,active,1]
     */
    public function is_not_unique(?string $str, string $field, array $data): bool
    {
        // Grab any data for exclusion of a single row.
        [$field, $whereField, $whereValue] = array_pad(explode(',', $field), 3, null);

        // Break the table and field apart
        sscanf($field, '%[^.].%[^.]', $table, $field);

        $row = Database::connect($data['DBGroup'] ?? null)
            ->table($table)
            ->select('1')
            ->where($field, $str)
            ->limit(1);

        if (! empty($whereField) && ! empty($whereValue) && ! preg_match('/^\{(\w+)\}$/', $whereValue)) {
            $row = $row->where($whereField, $whereValue);
        }

        return $row->get()->getRow() !== null;
    }

    /**
     * Value should be within an array of values
     */
    public function in_list(?string $value, string $list): bool
    {
        $list = array_map('trim', explode(',', $list));

        return in_array($value, $list, true);
    }

    /**
     * Checks the database to see if the given value is unique. Can
     * ignore a single record by field/value to make it useful during
     * record updates.
     *
     * Example:
     *    is_unique[table.field,ignore_field,ignore_value]
     *    is_unique[users.email,id,5]
     */
    public function is_unique(?string $str, string $field, array $data): bool
    {
        [$field, $ignoreField, $ignoreValue] = array_pad(explode(',', $field), 3, null);

        sscanf($field, '%[^.].%[^.]', $table, $field);

        $row = Database::connect($data['DBGroup'] ?? null)
            ->table($table)
            ->select('1')
            ->where($field, $str)
            ->limit(1);

        if (! empty($ignoreField) && ! empty($ignoreValue) && ! preg_match('/^\{(\w+)\}$/', $ignoreValue)) {
            $row = $row->where("{$ignoreField} !=", $ignoreValue);
        }

        return $row->get()->getRow() === null;
    }

    /**
     * Less than
     */
    public function less_than(?string $str, string $max): bool
    {
        return is_numeric($str) && $str < $max;
    }

    /**
     * Equal to or Less than
     */
    public function less_than_equal_to(?string $str, string $max): bool
    {
        return is_numeric($str) && $str <= $max;
    }

    /**
     * Matches the value of another field in $data.
     *
     * @param array $data Other field/value pairs
     */
    public function matches(?string $str, string $field, array $data): bool
    {
        if (strpos($field, '.') !== false) {
            return $str === dot_array_search($field, $data);
        }

        return array_key_exists($field, $data) && $str === $data[$field];
    }

    /**
     * Returns true if $str is $val or fewer characters in length.
     */
    public function max_length(?string $str, string $val): bool
    {
        return is_numeric($val) && $val >= mb_strlen($str ?? '');
    }

    /**
     * Returns true if $str is at least $val length.
     */
    public function min_length(?string $str, string $val): bool
    {
        return is_numeric($val) && $val <= mb_strlen($str ?? '');
    }

    /**
     * Does not equal the static value provided.
     *
     * @param string $str
     */
    public function not_equals(?string $str, string $val): bool
    {
        return $str !== $val;
    }

    /**
     * Value should not be within an array of values.
     *
     * @param string $value
     */
    public function not_in_list(?string $value, string $list): bool
    {
        return ! $this->in_list($value, $list);
    }

    /**
     * @param mixed $str
     */
    public function required($str = null): bool
    {
        if ($str === null) {
            return false;
        }

        if (is_object($str)) {
            return true;
        }

        if (is_array($str)) {
            return $str !== [];
        }

        return trim((string) $str) !== '';
    }

    /**
     * The field is required when any of the other required fields are present
     * in the data.
     *
     * Example (field is required when the password field is present):
     *
     *     required_with[password]
     *
     * @param string|null $str
     * @param string|null $fields List of fields that we should check if present
     * @param array       $data   Complete list of fields from the form
     */
    public function required_with($str = null, ?string $fields = null, array $data = []): bool
    {
        if ($fields === null || empty($data)) {
            throw new InvalidArgumentException('You must supply the parameters: fields, data.');
        }

        // If the field is present we can safely assume that
        // the field is here, no matter whether the corresponding
        // search field is present or not.
        $fields  = explode(',', $fields);
        $present = $this->required($str ?? '');

        if ($present) {
            return true;
        }

        // Still here? Then we fail this test if
        // any of the fields are present in $data
        // as $fields is the lis
        $requiredFields = [];

        foreach ($fields as $field) {
            if ((array_key_exists($field, $data) && ! empty($data[$field])) || (strpos($field, '.') !== false && ! empty(dot_array_search($field, $data)))) {
                $requiredFields[] = $field;
            }
        }

        return empty($requiredFields);
    }

    /**
     * The field is required when all of the other fields are present
     * in the data but not required.
     *
     * Example (field is required when the id or email field is missing):
     *
     *     required_without[id,email]
     *
     * @param string|null $str
     */
    public function required_without($str = null, ?string $fields = null, array $data = []): bool
    {
        if ($fields === null || empty($data)) {
            throw new InvalidArgumentException('You must supply the parameters: fields, data.');
        }

        // If the field is present we can safely assume that
        // the field is here, no matter whether the corresponding
        // search field is present or not.
        $fields  = explode(',', $fields);
        $present = $this->required($str ?? '');

        if ($present) {
            return true;
        }

        // Still here? Then we fail this test if
        // any of the fields are not present in $data
        foreach ($fields as $field) {
            if ((strpos($field, '.') === false && (! array_key_exists($field, $data) || empty($data[$field]))) || (strpos($field, '.') !== false && empty(dot_array_search($field, $data)))) {
                return false;
            }
        }

        return true;
    }

      //To check capacity during edit event for learners allocation serever side
      public function names_check($capacity = False, ?string &$error = null): bool {
        $count_allocated_learners = $this->eventmodel->fetch_event_allocated_learners($this->session->get('event_id_edit'));
        $choosed_capacity = $capacity;
        if ($count_allocated_learners > $choosed_capacity) {
            $error =  lang('app.language_school_event_capacity_server');
            return FALSE;
        } else {
            return TRUE;
        }
    }

        public function new_password_check($str = False, ?string &$error = null): bool {
            if (!preg_match("/[[:ascii:]]+/", $str)) {
                $error =  lang('app.language_site_booking_screen2_password_check');
                return FALSE;
            } else {
                return TRUE;
            }
        }
        
        public function time_check($error = null)
        {
            if ($this->request->getPost('start_date') == $this->request->getPost('end_date'))
            {
                if($this->request->getPost('end_time') == $this->request->getPost('start_time')) {
                 
                    $this->validation->setError('endtime_error', 'Start time must be greater than or equal ');

                    return  FALSE;
                } 
            }
            else
            {
                return TRUE;
            }
        }
        public function validate_current_time_based_on_timezone()
        {
            $start_date = $this->request->getPost('start_date');
            $start_time = $this->request->getPost('start_time');
            $timezone = $this->request->getPost('timezone');
    
            $date = new DateTime("now", new DateTimeZone($timezone));
    
            $current_date = $date->format('m/d/Y');
            $current_time = $date->format('H:i');
    
            if($start_date < $current_date) {
                $this->validation->setError('validate_current_time_based_on_timezone', 'Start time must be greater than or equal '.$current_date.' '.$current_time.'');
                return FALSE;
            } else if( $start_date == $current_date ) {
                if($start_time < $current_time) {
                    $this->validation->setError('validate_current_time_based_on_timezone', 'Start time must be greater than or equal '.$current_date.' '.$current_time.'');
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
               return TRUE;
            }
        }

        public function end_time_check()
        {
            if ($this->request->getPost('start_date') == $this->request->getPost('end_date'))
            {
               if($this->request->getPost('end_time') < $this->request->getPost('start_time')) {
                    $this->validation->setError('end_time_check', 'End time should be greater than start time');
                    return FALSE;
                }
            }
            else
            {
                return TRUE;
            }
        }

        public function check_downtime_exist()
        {
            if ($this->request->getPost('start_date') == $this->request->getPost('end_date') && $this->request->getPost('end_time') < $this->request->getPost('start_time') || $this->request->getPost('end_time') == $this->request->getPost('start_time'))
            {
                return TRUE;
            } else {
                $tz_from = $this->request->getPost('timezone');
                if($tz_from) {
                    if($this->request->getPost('start_date') && $this->request->getPost('end_date') ){
                        $start_date_array = strtotime($this->request->getPost('start_date'));
                        $start_date = date('Y-m-d', $start_date_array);
                        $end_date_array = strtotime($this->request->getPost('end_date'));
                        $end_date = date('Y-m-d', $end_date_array);
                    }
                    //convert date&time into UTC
                    $start_date_time = @utc_date_time($tz_from,$start_date,$this->request->getPost('start_time'));
                    $end_date_time = @utc_date_time($tz_from,$end_date,$this->request->getPost('end_time'));
                    if($this->request->getPost('id') != null){
                        $id = $this->request->getPost('id');
                    } else {
                        $id = FALSE;
                    }
                    $is_time_exist = $this->cmsmodel->is_down_time_exist($tz_from, $start_date_time, $end_date_time, $id);
                    if($is_time_exist) {
                        $this->validation->setError('check_downtime_exist', 'The given date and time fall in the existing scheduled time.');
                        return FALSE;
                    }
                } else {
                    return TRUE;
                }
            }
           
        }

        function check_integer($val= False, ?string &$error = null){
            if (!preg_match("/^[1-9]\d*$/", $val)) {       
                $this->validation->setError('check_integer', lang('app.language_admin_testform_field_check_integer'));            
                return false;            
            }        
        }


	// validation created for serbia language to allow characters from a-z and characters like
    public function serbia_username_check($str) {
        if (preg_match("/[!@#$%^&*,.<>=0-9]/", $str)) {
            $error =  lang('app.language_site_booking_screen2_firstname_check');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function isemail_check($str) {

        $result = is_email($str, true, true);

        if ($result === ISEMAIL_VALID) {
            return TRUE;
        } else if ($result < ISEMAIL_THRESHOLD) {
            $this->validation->setError('isemail_check', lang('app.form_validation_valid_email'));
            return FALSE;
        } else {
            $this->validation->setError('isemail_check', lang('app.form_validation_valid_email'));
            return FALSE;
        }
    }

    public function orgname_check($str) {
        if (preg_match("/^[\\s\\p{L}\p{M}()-:'&,. ]*$/u", $str)) {
            return TRUE;
        } else {
            $error =  lang('app.language_site_booking_screen2_orgname_check');
            return FALSE;
        }
    }

    function postal_check($str)
    {
        if (preg_match("/^[\\s\\p{L}\p{M}()\- ]*$/u", $str)) {
            return TRUE;
        }else{
            $error =  lang('app.language_site_booking_screen2_postal_check');
            return FALSE;
        }
    } 
    function phone_check($str)
    {
        if( ! preg_match("/^([0-9- ])+$/i", $str)){
            $error =  lang('app.language_site_booking_screen2_phone_check');
            return FALSE;
        }else{
            return TRUE;
        }
    }
    // form validate custom trim function
    function custom_trim($str, $field)
    {
        $original = $this->request->getVar();
        $original[$field] = trim($str);
        $this->request->setGlobal('post',$original);
        $this->request->setGlobal('request',$original);
        return TRUE;
    }

    function questionarie2_field_check($str)
    {
        if($str == ''){
            $error =  lang('app.language_questionarie2_label_error');
            return FALSE;
        }else{
            return TRUE;
        }
    }

     //Check UserId digit
     public function check_user_id()
     {
         $user_id = $this->request->getPost('user_id');
         $numlength = strlen((string)$user_id);
 
         if($numlength == 10 && is_numeric($user_id)) {
             return TRUE;
         } else {
             return FALSE;
         }
     }

    //Teacher Dashboard : To create group unique name validation 
    public function class_names_check($capacity = False, ?string &$error = null): bool {
        $class_id = $this->teachermodel->check_cls_name();
        if ($class_id->getNumRows() > 0) {
            $error =  lang('The Group name must contain a unique value..');
            return false;
        }else{
            return TRUE;
        }
    }

    public function name_check($str) {
        if (preg_match("/^[\\s\\p{L}\p{M}\- ]*$/u", $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function department_check($str) {
        if (preg_match("/^[\\s\\p{L}\p{M}()-:_ ]*$/u", $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


}
