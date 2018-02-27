<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `users` table method ------------------ */

    public function createUser($userName, $userEmail, $userPhoneNumber, $userPassword) {
        require_once 'PassHash.php';
        $response = array();

        // First check if user already existed in db!$this->isUserExists($email)
        if (1) {
            // Generating password hash
            $password_hash = PassHash::hash($userPassword);

            // Generating API key
            $api_key = $this->generateApiKey();

            $userStatus = 1;
            $lastUpdatedBy = "System";

            // insert query
            $stmt = $this->conn->prepare('INSERT INTO USERS(userApiKey,userName,userEmail,userPhoneNumber,userPassword,userStatus,lastUpdatedBy) VALUES(?,?,?,?,?,?,?)');
            $stmt->bind_param("sssssss", $api_key,$userName,$userEmail,$userPhoneNumber,$password_hash,$userStatus,$lastUpdatedBy);

            $result = $stmt->execute();

            // $lastrecorId = $this->conn->insert_id;

            // echo $lastrecorId;

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

     public function createStudioProfile($studioName, $studioLocation, $studioFacebookProfileuUrl, $studioOfficialUrl, $studioEmail, $studioPhoneNumber, $userId) {
        $response = array();

        if (1) {
            $stmt = $this->conn->prepare('INSERT INTO studioprofile(studioName, studioLocation, studioFacebookProfileuUrl, studioOfficialUrl, studioEmail, studioPhoneNumber, userId) VALUES(?,?,?,?,?,?, ?)');
            $stmt->bind_param("sssssss", $studioName, $studioLocation, $studioFacebookProfileuUrl, $studioOfficialUrl, $studioEmail, $studioPhoneNumber, $userId);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                $lastrecorId = $this->conn->insert_id;

                $stmt1 = $this->conn->prepare('INSERT INTO mostpopular(studioId) VALUES(?)');
                $stmt1->bind_param("s", $lastrecorId);
                $result1 = $stmt1->execute();
                $stmt1->close();
                return $lastrecorId;
            } else {
                // Failed to create user
                return "Failed";
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

    public function addAvailabilities($studioId, $availabilityId) {
    $stmt = $this->conn->prepare('INSERT INTO studiowithavailability(studioId, availabilityId) VALUES(?,?)');
            $stmt->bind_param("ss", $studioId, $availabilityId);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return "Passed";
            } else {
                // Failed to create user
                return "Failed";
            }
    }

    public function addEvents($studioId, $eventId) {
    $stmt = $this->conn->prepare('INSERT INTO studiowitheventnames(studioId, eventId) VALUES(?,?)');
            $stmt->bind_param("ss", $studioId, $eventId);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return "Passed";
            } else {
                // Failed to create user
                return "Failed";
            }
    }

    public function updateUserDetails($userId, $userName, $userEmail, $userPhoneNumber) {
    $stmt = $this->conn->prepare('UPDATE users SET userName = ?, userEmail = ?, userPhoneNumber = ? WHERE userId = ?');
            $stmt->bind_param("ssss", $userName, $userEmail, $userPhoneNumber, $userId);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return "Passed";
            } else {
                // Failed to create user
                return "Failed";
            }
    }
    /**
     * Checking user login
     */
    public function checkLogin($inputfromLogin, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT userPassword FROM users WHERE userEmail = ? OR userPhoneNumber = ?");

        $stmt->bind_param("ss",$inputfromLogin,$inputfromLogin);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return 1;
            } else {
                // user password is incorrect
                return 2;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return 3;
        }
    }

    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getEvents() {
       // $stmt = $this->conn->prepare("SELECT * FROM eventtypes ORDER BY eventId");
        $query = "SELECT * FROM eventtypes ORDER BY eventId";
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()){
                $data[] = $row;
        }
        //echo json_encode($data);
        return $data;
        }

    public function getAvailabilities() {
   // $stmt = $this->conn->prepare("SELECT * FROM eventtypes ORDER BY eventId");
    $query = "SELECT * FROM availableon ORDER BY availableId";
    $result = $this->conn->query($query);
    while ($row = $result->fetch_assoc()){
            $data[] = $row;
    }
    //echo json_encode($data);
    return $data;
    }

    public function getStudioDetails($userId) {
   // $stmt = $this->conn->prepare("SELECT * FROM eventtypes ORDER BY eventId");
        $stmt = $this->conn->prepare("SELECT * FROM studioprofile JOIN studiowithavailability
            ON studioprofile.studioId = studiowithavailability.studioId
            JOIN availableon
            ON studiowithavailability.availabilityId = availableon.availableId
            WHERE userId = ?");
        $stmt->bind_param("s", $userId);
        if ($stmt->execute()) {
            $studioDetails = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $data["studioDetails"][] = $studioDetails;
            if ($studioDetails['studioId'])
            {
                $query = "SELECT * FROM eventtypes JOIN  studiowitheventnames
                ON eventtypes.eventId = studiowitheventnames.eventId
                WHERE
                studiowitheventnames.studioId = '".$studioDetails['studioId']."'" ;
                $result = $this->conn->query($query);
                while ($row = $result->fetch_assoc()){
                        $data["eventDetails"][] = $row;
            }
            return $data;
        }
        } else {
            return NULL;
        }
    }

    public function getAllStudios() {
   // $stmt = $this->conn->prepare("SELECT * FROM eventtypes ORDER BY eventId");
        $query01 = "SELECT * FROM studioprofile JOIN studiowithavailability
            ON studioprofile.studioId = studiowithavailability.studioId
            JOIN availableon
            ON studiowithavailability.availabilityId = availableon.availableId" ;

        $result01 = $this->conn->query($query01);

        $studioDetails = array();
        while ($row1 = $result01->fetch_assoc()) {
            $data[] = $row1;
            //echo $row1['studioId'];
            if ($row1['studioId'])
            {
                $query = "SELECT * FROM eventtypes JOIN  studiowitheventnames
                ON eventtypes.eventId = studiowitheventnames.eventId
                WHERE
                studiowitheventnames.studioId = '".$row1['studioId']."'" ;
                $result = $this->conn->query($query);
                $eventDetails = array();
                while ($row = $result->fetch_assoc()){
                        //$data[]["eventDetails"] = $row;
                        $eventDetails[] = $row;
                        //echo json_encode($data["eventDetails"]);
            }
            }

            array_push($data, $eventDetails);
            array_push($studioDetails, $data);

            unset($eventDetails);
            unset($data);
        }

        return $studioDetails;
    }

    public function getEventDetails($studioId) {
   // $stmt = $this->conn->prepare("SELECT * FROM eventtypes ORDER BY eventId");
        $query = "SELECT * FROM eventtypes JOIN  studiowitheventnames
        ON eventtypes.eventId = studiowitheventnames.eventId
        WHERE
        studiowitheventnames.studioId = '".$studioId."'" ;
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()){
                $data["eventDetails"][] = $row;
        }
        //echo json_encode($data);
        return $data;
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmailOrUserName($inputfromLogin) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE userEmail = ? OR userPhoneNumber = ?");
        $stmt->bind_param("ss", $inputfromLogin, $inputfromLogin);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }


}

?>
