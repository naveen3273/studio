<?php

require_once '../api/DbHandler.php';
require_once '../api/PassHash.php';
require '../slim/vendor/autoload.php';

$app = new \Slim\App;

$user_id = NULL;

$app->post('/register', function($request, $response) {

    $response = array();

    $data = $request->getParsedBody();

    $userName = filter_var($data['userName'], FILTER_SANITIZE_STRING);
    $userEmail = filter_var($data['userEmail'], FILTER_SANITIZE_STRING);
    $userPhoneNumber = filter_var($data['userPhoneNumber'], FILTER_SANITIZE_STRING);
    $userPassword = filter_var($data['userPassword'], FILTER_SANITIZE_STRING);


    $db = new DbHandler();
    $res = $db->createUser($userName, $userEmail, $userPhoneNumber, $userPassword);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
                //echoRespnse(201, $response);
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
                //echoRespnse(200, $response);
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
                //echoRespnse(200, $response);
    }

    echo json_encode($response);
});

$app->post('/registerStudio', function($request, $response) {

    $response = array();

    $data = $request->getParsedBody();

    $studioName = filter_var($data['studioName'], FILTER_SANITIZE_STRING);
    $studioLocation = filter_var($data['studioLocation'], FILTER_SANITIZE_STRING);
    $studioFacebookProfileuUrl = filter_var($data['studioFacebookProfileuUrl'], FILTER_SANITIZE_STRING);
    $studioOfficialUrl = filter_var($data['studioOfficialUrl'], FILTER_SANITIZE_STRING);
    $studioEmail = filter_var($data['studioEmail'], FILTER_SANITIZE_STRING);
    $studioPhoneNumber = filter_var($data['studioPhoneNumber'], FILTER_SANITIZE_STRING);
    $userId = filter_var($data['userId'], FILTER_SANITIZE_STRING);


    $db = new DbHandler();
    $res = $db->createStudioProfile($studioName, $studioLocation, $studioFacebookProfileuUrl, $studioOfficialUrl, $studioEmail, $studioPhoneNumber, $userId);

    if ($res != "Failed") {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
        $response["createdStudioID"] = $res;
                //echoRespnse(201, $response);
    }  
    else {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
                //echoRespnse(200, $response);
    }

    echo json_encode($response);
});

$app->post('/addAvailabilities', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $studioId = filter_var($data['studioId'], FILTER_SANITIZE_STRING);
    $availabilityId = filter_var($data['availabilityId'], FILTER_SANITIZE_STRING);

    $db = new DbHandler();

    $res = $db->addAvailabilities($studioId, $availabilityId);
    if ($res != "Failed") {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
    }  
    else {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
                //echoRespnse(200, $response);
    }
    echo json_encode($response);
});

$app->post('/addEvents', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $studioId = filter_var($data['studioId'], FILTER_SANITIZE_STRING);
    $eventId = filter_var($data['eventId'], FILTER_SANITIZE_STRING);

    $db = new DbHandler();

    $res = $db->addEvents($studioId, $eventId);
    if ($res != "Failed") {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
    }  
    else {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
                //echoRespnse(200, $response);
    }
    echo json_encode($response);
});

$app->post('/updateUserDetails', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $userId = filter_var($data['userId'], FILTER_SANITIZE_STRING);
    $userName = filter_var($data['userName'], FILTER_SANITIZE_STRING);
    $userEmail = filter_var($data['userEmail'], FILTER_SANITIZE_STRING);
    $userPhoneNumber = filter_var($data['userPhoneNumber'], FILTER_SANITIZE_STRING);
    //echo $userId, $userName, $userEmail, $userPhoneNumber;
    $db = new DbHandler();

    $res = $db->updateUserDetails($userId, $userName, $userEmail, $userPhoneNumber);
    if ($res != "Failed") {
        $response["error"] = false;
        $response["message"] = "Updated successfully";
    }  
    else {
        $response["error"] = true;
        $response["message"] = "Could not able to update";
                //echoRespnse(200, $response);
    }
    echo json_encode($response);
});

$app->post('/login', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $inputfromLogin = filter_var($data['inputfromLogin'], FILTER_SANITIZE_STRING);
    $userPassword = filter_var($data['userPassword'], FILTER_SANITIZE_STRING);

    $db = new DbHandler();

    if ($db->checkLogin($inputfromLogin, $userPassword) == 1) {
        $user = $db->getUserByEmailOrUserName($inputfromLogin);
        $response['userName'] = $user['userName'];
        $response['userId'] = $user['userId'];
        $response['userApiKey'] = $user['userApiKey'];
        $response['userEmail'] = $user['userEmail'];
        $response['userPhoneNumber'] = $user['userPhoneNumber'];
        $response['lastUpdatedOn'] = $user['lastUpdatedOn'];
        $response['lastUpdatedBy'] = $user['lastUpdatedBy'];
        $response['error'] = false;
        $response['message'] = 'Login succesfull.';
    }
    else if ($db->checkLogin($inputfromLogin, $userPassword) == 2){
        $response['error'] = false;
        $response['message'] = 'Login failed. Incorrect credentials';
    }
    else {
        $response['error'] = true;
        $response['message'] = 'User not exist.';
    }

    echo json_encode($response);
});

$app->get('/getEvents', function($request, $response) {

    $response = array();

    $db = new DbHandler();

    $response = $db->getEvents();

    echo json_encode($response);
});

$app->get('/getAvailabilities', function($request, $response) {

    $response = array();

    $db = new DbHandler();

    $response = $db->getAvailabilities();

    echo json_encode($response);
});

$app->get('/getAllStudios', function($request, $response) {

    $response = array();

    $db = new DbHandler();

    $response = $db->getAllStudios();

    echo json_encode($response);
});

$app->post('/getStudioDetails', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $userId = filter_var($data['userId'], FILTER_SANITIZE_STRING);

    $db = new DbHandler();

    $response = $db->getStudioDetails($userId);

    echo json_encode($response);
});

$app->post('/getEventDetails', function($request, $response) {

    $response = array();
    $data = $request->getParsedBody();

    $studioId = filter_var($data['studioId'], FILTER_SANITIZE_STRING);

    $db = new DbHandler();

    $response = $db->getEventDetails($studioId);

    //$response["eventDetails"] = $response;

    echo json_encode($response);
});


$app->run();
?>