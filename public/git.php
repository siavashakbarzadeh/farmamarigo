<?php

    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? false;

    // Collect the data from the POST
    $payload = file_get_contents('php://input');

    // If a secret key has been set, verify the payload
    if ($signature) {
        list($algo, $github_signature) = explode('=', $signature, 2);
        $hash = hash_hmac($algo, $payload, $secret);

        if (!hash_equals($hash, $github_signature)) {
            header('HTTP/1.0 403 Forbidden');
            die('Forbidden: Invalid signature');
        }
    }

    // Parse the payload
    parse_str($payload, $data);

    // Assuming the parsed payload data has a key 'payload' with the JSON string
    $json = $data['payload'] ?? false;
    $decoded_payload = $json ? json_decode($json) : [];

    // If decoding fails, respond with an error
    if (!$decoded_payload) {
        header('HTTP/1.0 400 Bad Request');
        die('Bad Request: Decoding JSON failed');
    }

    // The commands
    $commands = array(
        'cd ..', // Assuming the script is in the 'public' directory and the repo is in the parent directory
        'git pull'
    );

    // Execute the commands
    foreach ($commands as $command) {
        shell_exec($command);
    }

    // Respond to the request indicating success
    header('HTTP/1.0 200 OK');
    echo "Success";
    //deployed successfully
    //awo
?>
