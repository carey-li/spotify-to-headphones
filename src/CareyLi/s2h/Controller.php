<?php

namespace CareyLi\s2h;

/**
 * Main controller
 */
class Controller
{

	private $model;
	private $view;

    public function __construct(Model $model, \League\Plates\Engine $view)
    {
    	$this->model = $model;
    	$this->view = $view;
    }

    public function index() {
    	if (!$this->model->spotifyAccessTokensExist()) {
    		echo $this->view->render("spotify_oauth_info", array(
    			"authorize_url" => $this->model->getSpotifyAuthorizeUrl()
    		));
    	} else {
    		echo $this->view->render("index");
    	}
    }

    public function firstRun() {
    	if (!$this->model->isFirstRun()) {
			header("Location: /");
			exit;
		}

		echo $this->view->render("firstrun", array(
			"redirect_url" => $_SERVER['HTTP_HOST'] . "/spotify_callback/"
		));
    }

    public function firstRunSave() {
    	$request = $_POST;

		if (count(array_diff(["headphones_host", "headphones_key", "client_id", "client_secret"], array_keys($request))) > 0) {
			throw new \Exception("Insufficent arguments passed to save settings.");
		}

		if ($this->model->updateSettings($request)) {
			echo '<meta http-equiv="refresh" content="0; url=./">';
			exit;
		}
    }

    public function spotify_oauth() {
    	if (isset($_GET['code'])) {
    		$this->model->setSpotifyAccessToken($_GET['code']);
    	} else {
    		echo 'MISSING SPOTIFY ACCESS TOKEN';
    		exit;
    	}
    }

    public function settings() {
    	echo $this->view->render("settings");
    }
}