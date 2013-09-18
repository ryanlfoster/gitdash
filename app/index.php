<?php

    include 'lib.php';

    $auth_url = 'https://github.com/login/oauth/authorize?client_id=d9ff044e5a5eba36673b&redirect_uri=http://localhost:8888/oauth_redirect&scope=user,repo';

    $app->get('/?', function() use ($auth_url)  {
        echo "<a href='".$auth_url."'>Log in with Github</a>";
    });

    $app->get('/oauth_redirect/?', function() use ($app) {
        $code = $app->request()->params('code');
        $token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : get_access_token($code);

        $app->redirect('/app');
    });

    $app->get('/api/repos/?', function() use ($app) {
        echo get_json('https://api.github.com/orgs/Betterment/repos?type=private');
    });

    $app->get('/api/pulls/?', function() use ($app) {
        $pull_requests = array();
        $repos = get_data('https://api.github.com/orgs/Betterment/repos?type=private');

        foreach($repos as $repo) {
            $pulls = get_data('https://api.github.com/repos/Betterment/'.$repo->name.'/pulls');
            if(count($pulls)) {
                foreach($pulls as $pr) {
                    $pull_requests[] = get_data('https://api.github.com/repos/Betterment/'.$repo->name.'/pulls/'.$pr->number);
                }
            }
        }

        echo json_encode($pull_requests);
    });

    $app->get('/app/?', function() use ($app) {
        include 'header.php';
        include 'footer.php';
    });

    $app->run();
